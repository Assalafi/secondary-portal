<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\AcademicSession;
use App\Models\Payment;
use App\Models\PaymentSetup;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdmissionApplicationController extends Controller
{
    /**
     * Display all applications
     */
    public function index()
    {
        $applications = AdmissionApplication::where('parent_id', Auth::id())
            ->with(['proposedClass', 'academicSession', 'payment'])
            ->latest()
            ->paginate(20);

        return view('parent.admission.index', compact('applications'));
    }

    /**
     * Show create application page (payment first)
     */
    public function create()
    {
        // Check if there's a pending payment application
        $pendingApplication = AdmissionApplication::where('parent_id', Auth::id())
            ->where('status', 'Pending Payment')
            ->first();

        if ($pendingApplication) {
            return redirect()->route('parent.admission.payment', $pendingApplication->id)
                ->with('info', 'You have a pending admission application payment. Please complete it before creating a new application.');
        }

        // Get application fee from payment_setup
        $paymentSetup = PaymentSetup::where('payment_type', 'Application')
            ->where('status', 'Active')
            ->first();
        
        $applicationFee = $paymentSetup ? $paymentSetup->amount : 5000;

        return view('parent.admission.create', compact('applicationFee'));
    }

    /**
     * Initiate payment for application - Generate RRR and create invoice immediately
     */
    public function initiatePayment(Request $request)
    {
        Log::info('=== ADMISSION PAYMENT INITIATION STARTED ===', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Get payment setup
            $paymentSetup = PaymentSetup::where('payment_type', 'Application')
                ->where('status', 'Active')
                ->first();
            
            if (!$paymentSetup) {
                Log::error('Payment setup not found for Application type');
                DB::rollBack();
                return back()->with('error', 'Application fee setup not found. Please contact administrator.');
            }

            Log::info('Payment setup found', ['id' => $paymentSetup->id, 'amount' => $paymentSetup->amount]);

            $applicationFee = $paymentSetup->amount;

            // Get current academic session
            $currentSession = AcademicSession::where('is_current', true)->first();
            if (!$currentSession) {
                $currentSession = AcademicSession::first();
            }

            if (!$currentSession) {
                Log::error('No academic session found');
                DB::rollBack();
                return back()->with('error', 'No academic session found. Please contact administrator.');
            }

            // Generate application number first
            $applicationNumber = AdmissionApplication::generateApplicationNumber();
            Log::info('Generated application number', ['number' => $applicationNumber]);

            // Generate RRR from Remita
            $orderId = 'SS_ADM_' . strtoupper(uniqid());
            $description = 'Admission Application Fee - ' . $applicationNumber;

            Log::info('Calling Remita API to generate RRR...');
            $remitaData = $this->generateRRR([
                'amount' => $applicationFee,
                'orderId' => $orderId,
                'payerName' => $user->name,
                'payerEmail' => $user->email,
                'payerPhone' => $user->phone ?? 'N/A',
                'description' => $description,
            ]);

            if (!$remitaData['success']) {
                Log::error('Failed to generate RRR', ['error' => $remitaData['message'] ?? 'Unknown error']);
                DB::rollBack();
                return back()->with('error', 'Failed to generate payment reference: ' . ($remitaData['message'] ?? 'Unknown error'));
            }

            $rrr = $remitaData['RRR'];
            Log::info('RRR generated successfully', ['RRR' => $rrr]);

            // Create invoice with RRR
            $invoice = Invoice::create([
                'invoice_number' => $rrr,
                'student_id' => null,
                'academic_session_id' => $currentSession->id,
                'term_id' => null,
                'total_amount' => $applicationFee,
                'amount_paid' => 0,
                'balance' => $applicationFee,
                'due_date' => now()->addDays(30),
                'status' => 'Pending',
                'notes' => 'Admission Application Fee',
                'metadata' => json_encode([
                    'type' => 'admission_application',
                    'parent_id' => $user->id,
                    'parent_name' => $user->name,
                    'parent_email' => $user->email,
                    'application_number' => $applicationNumber,
                    'orderId' => $orderId,
                    'payment_method' => 'Remita',
                    'RRR' => $rrr,
                    'created_at' => now()->toDateTimeString(),
                ]),
            ]);

            Log::info('Invoice created', ['invoice_id' => $invoice->id, 'RRR' => $rrr]);

            // Create invoice item
            DB::table('invoice_items')->insert([
                'invoice_id' => $invoice->id,
                'payment_setup_id' => $paymentSetup->id,
                'amount' => $applicationFee,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Invoice item created');

            // Create application with invoice
            $application = AdmissionApplication::create([
                'application_number' => $applicationNumber,
                'parent_id' => $user->id,
                'invoice_id' => $invoice->id,
                'status' => 'Pending Payment',
                // Temporary data (will be filled after payment)
                'first_name' => 'Pending',
                'last_name' => 'Pending',
                'date_of_birth' => now(),
                'gender' => 'Male',
                'proposed_class_id' => 1, // Temporary
                'academic_session_id' => $currentSession->id,
                'guardian_name' => $user->name,
                'guardian_phone' => $user->phone ?? 'N/A',
                'guardian_email' => $user->email,
            ]);

            Log::info('Application created', ['application_id' => $application->id, 'number' => $applicationNumber]);

            // Store RRR in session for easy access
            session([
                'remita_payment_' . $rrr => [
                    'type' => 'admission_application',
                    'data' => [
                        'application_id' => $application->id,
                        'application_number' => $applicationNumber,
                        'amount' => $applicationFee,
                    ],
                    'orderId' => $orderId,
                    'amount' => $applicationFee,
                    'invoice_id' => $invoice->id,
                    'invoice_ids' => [$invoice->id],
                    'application_id' => $application->id,
                ]
            ]);

            DB::commit();

            Log::info('=== ADMISSION PAYMENT INITIATION COMPLETED SUCCESSFULLY ===', [
                'application_id' => $application->id,
                'invoice_id' => $invoice->id,
                'RRR' => $rrr,
                'redirect_to' => route('parent.admission.payment', $application->id)
            ]);

            return redirect()->route('parent.admission.payment', $application->id)
                ->with('success', '✅ Payment reference generated! RRR: ' . $rrr . ' - Please complete your payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== ADMISSION PAYMENT INITIATION FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to initiate application: ' . $e->getMessage());
        }
    }

    /**
     * Generate Remita RRR (matching PaymentController implementation)
     */
    private function generateRRR($data)
    {
        try {
            $merchantId = config('remita.merchant_id');
            $serviceTypeId = config('remita.service_type_id');
            $apiKey = config('remita.api_key');
            $gatewayUrl = config('remita.gateway_url');

            // Validate required fields
            $requiredFields = ['amount', 'orderId', 'payerName', 'payerEmail', 'payerPhone', 'description'];
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                Log::error('RRR Generation: Missing required fields', ['missing' => $missingFields, 'data' => $data]);
                return [
                    'success' => false,
                    'message' => 'Missing required fields: ' . implode(', ', $missingFields)
                ];
            }

            $payload = [
                'serviceTypeId' => $serviceTypeId,
                'amount' => $data['amount'],
                'orderId' => $data['orderId'],
                'payerName' => $data['payerName'],
                'payerEmail' => $data['payerEmail'],
                'payerPhone' => $data['payerPhone'],
                'description' => $data['description'],
            ];

            $hash = hash('sha512', $merchantId . $serviceTypeId . $data['orderId'] . $data['amount'] . $apiKey);

            Log::info('Remita RRR Request', [
                'merchantId' => $merchantId,
                'serviceTypeId' => $serviceTypeId,
                'orderId' => $data['orderId'],
                'amount' => $data['amount'],
                'url' => $gatewayUrl . '/echannelsvc/merchant/api/paymentinit',
                'payload' => $payload
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $hash,
            ])->post($gatewayUrl . '/echannelsvc/merchant/api/paymentinit', $payload);

            // Get raw response body
            $rawBody = $response->body();
            
            Log::info('Remita RRR Raw Response', [
                'status' => $response->status(),
                'raw_body' => $rawBody,
                'headers' => $response->headers()
            ]);

            // Try to parse as JSON first
            $responseBody = $response->json();
            
            // If JSON parsing failed or returned null, try to extract from JSONP
            if (empty($responseBody) && !empty($rawBody)) {
                // Check if it's a JSONP response (format: jsonp12345({...}))
                if (preg_match('/\{.*\}/', $rawBody, $matches)) {
                    $jsonString = $matches[0];
                    $responseBody = json_decode($jsonString, true);
                    Log::info('Extracted from JSONP', ['body' => $responseBody]);
                }
            }
            
            Log::info('Remita RRR Response Parsed', [
                'status' => $response->status(),
                'body' => $responseBody
            ]);

            if ($response->successful()) {
                if (isset($responseBody['RRR'])) {
                    Log::info('RRR Generated Successfully', ['RRR' => $responseBody['RRR']]);
                    return [
                        'success' => true,
                        'RRR' => $responseBody['RRR'],
                        'statuscode' => $responseBody['statuscode'] ?? null,
                    ];
                } else {
                    Log::error('RRR Generation: No RRR in response', ['response' => $responseBody]);
                    return [
                        'success' => false,
                        'message' => 'No RRR returned. ' . ($responseBody['statusMessage'] ?? 'Unknown error')
                    ];
                }
            }

            // Handle error responses
            $errorMessage = 'Failed to generate RRR';
            if (isset($responseBody['statusMessage'])) {
                $errorMessage .= ': ' . $responseBody['statusMessage'];
            }
            if (isset($responseBody['statuscode'])) {
                $errorMessage .= ' (Code: ' . $responseBody['statuscode'] . ')';
            }
            
            Log::error('RRR Generation Failed', [
                'status_code' => $response->status(),
                'response' => $responseBody,
                'error' => $errorMessage
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('RRR Generation Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Error generating payment reference: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Show payment page
     */
    public function showPayment(AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        if ($application->status !== 'Pending Payment') {
            return redirect()->route('parent.admission.show', $application->id);
        }

        // Load invoice - it must exist at this point
        $invoice = $application->invoice;
        
        if (!$invoice) {
            return redirect()->route('parent.admission.create')
                ->with('error', 'Invoice not found. Please try again.');
        }

        // Get RRR and orderId from invoice metadata
        $metadata = json_decode($invoice->metadata, true);
        $rrr = $metadata['RRR'] ?? null;
        $orderId = $metadata['orderId'] ?? null;

        if (!$rrr) {
            return redirect()->route('parent.admission.create')
                ->with('error', 'Payment reference not found. Please try again.');
        }

        $applicationFee = $invoice->balance;

        return view('parent.admission.payment', compact('application', 'invoice', 'applicationFee', 'rrr', 'orderId'));
    }


    /**
     * Show application form
     */
    public function showForm(AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        if (!$application->isPaid()) {
            return redirect()->route('parent.admission.payment', $application->id);
        }

        if (!$application->canEdit()) {
            return redirect()->route('parent.admission.show', $application->id);
        }

        $classes = SchoolClass::orderBy('name')->get();
        $classArms = ClassArm::with('schoolClass')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('parent.admission.form', compact('application', 'classes', 'classArms', 'academicSessions'));
    }

    /**
     * Save/Update application form
     */
    public function saveForm(Request $request, AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        if (!$application->canEdit()) {
            return back()->with('error', 'This application can no longer be edited.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:Male,Female',
            'nationality' => 'required|string|max:255',
            'state_of_origin' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'home_address' => 'nullable|string',
            'religion' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'medical_conditions' => 'nullable|string',
            'proposed_class_id' => 'required|exists:school_classes,id',
            'proposed_class_arm_id' => 'nullable|exists:class_arms,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'previous_school' => 'nullable|string|max:255',
            'reason_for_admission' => 'nullable|string',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'required|email|max:255',
            'guardian_occupation' => 'nullable|string|max:255',
            'guardian_address' => 'nullable|string',
            'guardian_relationship' => 'required|in:Father,Mother,Guardian,Uncle,Aunt,Grandparent,Other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'passport_photo' => 'nullable|image|max:2048',
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'previous_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['passport_photo', 'birth_certificate', 'previous_report']);

            // Handle file uploads
            if ($request->hasFile('passport_photo')) {
                if ($application->passport_photo_path) {
                    Storage::delete($application->passport_photo_path);
                }
                $data['passport_photo_path'] = $request->file('passport_photo')->store('admission/photos', 'public');
            }

            if ($request->hasFile('birth_certificate')) {
                if ($application->birth_certificate_path) {
                    Storage::delete($application->birth_certificate_path);
                }
                $data['birth_certificate_path'] = $request->file('birth_certificate')->store('admission/certificates', 'public');
            }

            if ($request->hasFile('previous_report')) {
                if ($application->previous_report_path) {
                    Storage::delete($application->previous_report_path);
                }
                $data['previous_report_path'] = $request->file('previous_report')->store('admission/reports', 'public');
            }

            $application->update($data);

            DB::commit();

            return back()->with('success', 'Application saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save application: ' . $e->getMessage());
        }
    }

    /**
     * Submit application
     */
    public function submit(AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        if (!$application->canEdit()) {
            return back()->with('error', 'This application can no longer be submitted.');
        }

        // Validate required fields are filled
        if (!$application->first_name || $application->first_name === 'Pending') {
            return back()->with('error', 'Please complete the application form before submitting.');
        }

        // Validate required documents are uploaded
        if (!$application->passport_photo_path) {
            return back()->with('error', 'Please upload a Passport Photograph before submitting.');
        }

        if (!$application->birth_certificate_path) {
            return back()->with('error', 'Please upload a Birth Certificate before submitting.');
        }

        // Validate other essential fields
        $missingFields = [];
        if (!$application->date_of_birth) $missingFields[] = 'Date of Birth';
        if (!$application->gender) $missingFields[] = 'Gender';
        if (!$application->nationality) $missingFields[] = 'Nationality';
        if (!$application->proposed_class_id) $missingFields[] = 'Proposed Class';
        if (!$application->academic_session_id) $missingFields[] = 'Academic Session';
        if (!$application->guardian_name) $missingFields[] = 'Guardian Name';
        if (!$application->guardian_phone) $missingFields[] = 'Guardian Phone';
        if (!$application->guardian_email) $missingFields[] = 'Guardian Email';

        if (!empty($missingFields)) {
            return back()->with('error', 'Please complete the following required fields: ' . implode(', ', $missingFields));
        }

        $application->update([
            'status' => 'Submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('parent.admission.show', $application->id)
            ->with('success', 'Application submitted successfully! The admin will review it soon.');
    }

    /**
     * Show application details
     */
    public function show(AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        // Only allow viewing if application has been paid OR submitted
        $isPaid = ($application->invoice && $application->invoice->status === 'Paid') || 
                  ($application->payment && $application->payment->status === 'Completed');
        
        $isSubmittedOrReviewed = in_array($application->status, ['Submitted', 'Approved', 'Rejected']);

        // Redirect to form only if BOTH not paid AND not submitted
        if (!$isPaid && !$isSubmittedOrReviewed) {
            return redirect()->route('parent.admission.form', $application->id)
                ->with('info', 'Please complete your application first.');
        }

        $application->load(['proposedClass', 'proposedClassArm', 'academicSession', 'payment', 'invoice', 'reviewer']);

        return view('parent.admission.show', compact('application'));
    }

    /**
     * Download application PDF
     */
    public function downloadPdf(AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships
        $application->load(['proposedClass', 'proposedClassArm', 'academicSession', 'invoice']);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('parent.admission.pdf', compact('application'));
        
        // Download with filename (sanitize application number to remove / and \)
        $sanitizedAppNumber = str_replace(['/', '\\'], '_', $application->application_number);
        $filename = 'Application_' . $sanitizedAppNumber . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Delete application (only if draft or pending payment)
     */
    public function destroy(AdmissionApplication $application)
    {
        // Ensure parent owns this application
        if ($application->parent_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($application->status, ['Draft', 'Pending Payment'])) {
            return back()->with('error', 'This application cannot be deleted.');
        }

        // Delete uploaded files
        if ($application->passport_photo_path) {
            Storage::delete($application->passport_photo_path);
        }
        if ($application->birth_certificate_path) {
            Storage::delete($application->birth_certificate_path);
        }
        if ($application->previous_report_path) {
            Storage::delete($application->previous_report_path);
        }

        $application->delete();

        return redirect()->route('parent.admission.index')
            ->with('success', 'Application deleted successfully.');
    }
}
