<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FeeSetup;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\SessionTerm;
use App\Models\PaymentSetup;
use App\Models\Student;
use App\Models\SchoolSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments for all dependents.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $dependents = $user->dependents()->with(['user', 'classArm.schoolClass'])->get();
        $dependentIds = $dependents->pluck('id');
        
        // Get sessions and terms from Session/Term settings source
        $configuredSessionNames = SessionTerm::query()
            ->where('status', 'Active')
            ->distinct()
            ->pluck('academic_year');
        $configuredTermNames = SessionTerm::query()
            ->where('status', 'Active')
            ->distinct()
            ->pluck('term_name');

        $sessions = $configuredSessionNames->isNotEmpty()
            ? AcademicSession::whereIn('name', $configuredSessionNames)->orderBy('name', 'desc')->get()
            : AcademicSession::orderBy('name', 'desc')->get();
        $terms = $configuredTermNames->isNotEmpty()
            ? Term::whereIn('name', $configuredTermNames)->orderBy('number')->get()
            : Term::orderBy('number')->get();
        
        // Get filter values
        $filterSession = $request->input('session');
        $filterTerm = $request->input('term');
        $filterStudent = $request->input('student');
        
        // Build query for pending invoices
        $pendingQuery = Invoice::whereIn('student_id', $dependentIds)
            ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
            ->with(['student.user', 'term', 'academicSession', 'invoiceItems.feeSetup']);
        
        if ($filterSession) {
            $pendingQuery->where('academic_session_id', $filterSession);
        }
        if ($filterTerm) {
            $pendingQuery->where('term_id', $filterTerm);
        }
        if ($filterStudent) {
            $pendingQuery->where('student_id', $filterStudent);
        }
        
        // Build query for history invoices
        $historyQuery = Invoice::whereIn('student_id', $dependentIds)
            ->where('status', 'Paid')
            ->with(['student.user', 'student.classArm.schoolClass', 'term', 'academicSession', 'invoiceItems.feeSetup', 'payments']);
        
        if ($filterSession) {
            $historyQuery->where('academic_session_id', $filterSession);
        }
        if ($filterTerm) {
            $historyQuery->where('term_id', $filterTerm);
        }
        if ($filterStudent) {
            $historyQuery->where('student_id', $filterStudent);
        }
        
        // Get all invoices grouped by status
        $payments = [
            'pending' => $pendingQuery->latest()->get(),
            'history' => $historyQuery->latest()->get(),
        ];
        
        $summary = [
            'total_pending' => Invoice::whereIn('student_id', $dependentIds)
                ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
                ->sum('balance'),
            'pending_count' => Invoice::whereIn('student_id', $dependentIds)
                ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
                ->count(),
            'total_paid' => Invoice::whereIn('student_id', $dependentIds)
                ->where('status', 'Paid')
                ->sum('amount_paid'),
        ];
        
        // Get other services from payment_setups (exclude School Fees)
        $otherServices = PaymentSetup::where('payment_type', '!=', 'School Fees')
            ->where('status', 'Active')
            ->get();
        
        return view('parent.payments.index', compact('payments', 'summary', 'dependents', 'sessions', 'terms', 'filterSession', 'filterTerm', 'filterStudent', 'otherServices'));
    }
    
    /**
     * Display details of a specific invoice/payment.
     */
    public function show($id)
    {
        $user = Auth::user();
        $dependentIds = $user->dependents()->pluck('students.id');
        
        $invoice = Invoice::whereIn('student_id', $dependentIds)
            ->with(['student.user', 'payments', 'invoiceItems'])
            ->findOrFail($id);
        
        return view('parent.payments.show', compact('invoice'));
    }
    
    /**
     * Download payment receipt as PDF
     */
    public function downloadReceipt($id)
    {
        $user = Auth::user();
        $dependentIds = $user->dependents()->pluck('students.id');
        
        // Get invoice with all relationships
        $invoice = Invoice::whereIn('student_id', $dependentIds)
            ->with([
                'student.user', 
                'student.classArm.schoolClass', 
                'term', 
                'academicSession', 
                'invoiceItems.feeSetup',
                'payments'
            ])
            ->findOrFail($id);
        
        // Check if invoice is paid
        if ($invoice->status !== 'Paid') {
            return back()->with('error', 'Receipt can only be downloaded for paid invoices.');
        }
        
        // Get payment record
        $payment = $invoice->payments()->latest()->first();
        
        // Get school settings
        $schoolSettings = SchoolSettings::first();
        if (!$schoolSettings) {
            // Create default settings if none exist
            $schoolSettings = SchoolSettings::create([
                'school_name' => 'Secondary School Portal',
                'school_address' => 'School Address',
                'phone_number' => 'N/A',
                'email' => 'info@school.com'
            ]);
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('parent.payments.receipt', compact('invoice', 'payment', 'schoolSettings'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Stream PDF to browser (opens in browser instead of downloading)
        $fileName = 'Receipt_' . $invoice->invoice_number . '_' . date('Y-m-d') . '.pdf';
        return $pdf->stream($fileName);
    }
    
    /**
     * Initialize Remita payment and generate RRR
     */
    public function initiateRemita(Request $request)
    {
        try {
            $user = Auth::user();
            $type = $request->type; // 'school_fees', 'other_services', 'invoices'
            $data = $request->data;
            
            // Validate user has required information
            if (empty($user->name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Full name is required. Please update your profile.'
                ], 400);
            }
            
            if (empty($user->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email address is required. Please update your profile.'
                ], 400);
            }
            
            if (empty($user->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number is required. Please update your profile.'
                ], 400);
            }
            
            $amount = 0;
            $description = '';
            $invoiceIds = [];
            
            // Calculate amount based on payment type
            if ($type === 'invoices') {
                if (empty($data) || !is_array($data)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No invoices selected for payment'
                    ], 400);
                }
                
                foreach ($data as $payment) {
                    $amount += $payment['amount'];
                    $invoiceIds[] = $payment['id'];
                }
                $description = 'Payment for ' . count($data) . ' invoice(s)';
            } elseif ($type === 'school_fees') {
                // Validate school fees data
                if (empty($data['students']) || empty($data['terms']) || empty($data['sessions'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select students, terms, and sessions for school fees payment'
                    ], 400);
                }
                
                $studentCount = count($data['students']);
                $termCount = count($data['terms']);
                $sessionCount = count($data['sessions']);
                
                // Check for existing invoices to prevent duplicates (only for existing sessions/terms)
                $existingInvoices = [];
                foreach ($data['students'] as $studentId) {
                    foreach ($data['sessions'] as $sessionNameOrId) {
                        // Try to find session by name or ID
                        $session = is_numeric($sessionNameOrId) 
                            ? AcademicSession::find($sessionNameOrId)
                            : AcademicSession::where('name', $sessionNameOrId)->first();
                        
                        if (!$session) {
                            // Session doesn't exist yet, will be auto-created - skip duplicate check
                            continue;
                        }
                        
                        foreach ($data['terms'] as $termName) {
                            // Get term ID from term name
                            $termMapping = [
                                '1st term' => '1st Term',
                                '2nd term' => '2nd Term',
                                '3rd term' => '3rd Term',
                            ];
                            $mappedTerm = $termMapping[strtolower($termName)] ?? $termName;
                            
                            $term = Term::where('name', $mappedTerm)->first();
                            if (!$term) {
                                // Term doesn't exist yet, will be auto-created - skip duplicate check
                                continue;
                            }
                            
                            // Check if invoice already exists for this combination
                            $existingInvoice = Invoice::where('student_id', $studentId)
                                ->where('academic_session_id', $session->id)
                                ->where('term_id', $term->id)
                                ->whereIn('status', ['Pending', 'Partial', 'Paid'])
                                ->first();
                            
                            if ($existingInvoice) {
                                $student = Student::with('user')->find($studentId);
                                $existingInvoices[] = [
                                    'student' => $student->user->name ?? 'Unknown',
                                    'session' => $session->name,
                                    'term' => $term->name,
                                    'invoice_number' => $existingInvoice->invoice_number,
                                    'status' => $existingInvoice->status
                                ];
                            }
                        }
                    }
                }
                
                // If any existing invoices found, return error
                if (!empty($existingInvoices)) {
                    $errorMessage = "Cannot create invoice. The following invoices already exist:\n\n";
                    foreach ($existingInvoices as $existing) {
                        $errorMessage .= "• {$existing['student']} - {$existing['session']}, {$existing['term']}\n";
                        $errorMessage .= "  Invoice: {$existing['invoice_number']} (Status: {$existing['status']})\n\n";
                    }
                    $errorMessage .= "Please use the existing invoice or contact the administrator.";
                    
                    Log::warning('Duplicate invoice attempt', [
                        'existing_invoices' => $existingInvoices,
                        'user' => $user->name
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'existing_invoices' => $existingInvoices
                    ], 400);
                }
                
                // Fetch fee amounts from payment_setups table
                $missingFees = [];
                $totalAmount = 0;
                
                // Loop through each student to get their specific level
                foreach ($data['students'] as $studentId) {
                    $student = Student::with('classArm.schoolClass')->find($studentId);
                    
                    if (!$student) {
                        continue;
                    }
                    
                    // Get student's class level (e.g., JSS 1, SS 2)
                    $classLevel = $student->classArm->schoolClass->level ?? null;
                    
                    if (!$classLevel) {
                        Log::warning('Student has no class assigned', ['student_id' => $studentId]);
                        continue;
                    }
                    
                    // Loop through each term and session for calculation
                    foreach ($data['terms'] as $termName) {
                        foreach ($data['sessions'] as $sessionNameOrId) {
                            $paymentSetup = PaymentSetup::schoolFeeFor($classLevel, $termName);
                            
                            if ($paymentSetup) {
                                $totalAmount += $paymentSetup->amount;
                            } else {
                                $missingFees[] = [
                                    'student' => $student->user->name ?? 'Unknown',
                                    'level' => $classLevel,
                                ];
                            }
                        }
                    }
                }
                
                // If any fees are missing, return error
                if (!empty($missingFees)) {
                    $errorMessage = "Fee setup not configured in payment_setups table for:\n";
                    foreach ($missingFees as $missing) {
                        $errorMessage .= "- {$missing['student']} ({$missing['level']})\n";
                    }
                    $errorMessage .= "\nPlease contact the school administrator to configure fee amounts in Payment Setup for this class level/term, or use Term = All.";
                    
                    Log::warning('Missing payment setup configuration', [
                        'missing_fees' => $missingFees,
                        'user' => $user->name
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'missing_fees' => $missingFees
                    ], 400);
                }
                
                if ($totalAmount <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not calculate payment amount. Please ensure fee setup is configured properly.'
                    ], 400);
                }
                
                $amount = $totalAmount;
                $description = "School Fees Payment: {$studentCount} student(s), {$termCount} term(s), {$sessionCount} session(s)";
            } elseif ($type === 'other_services') {
                // Check for new format (multiple students)
                if (!empty($data['students']) && !empty($data['amount_per_student'])) {
                    $studentCount = count($data['students']);
                    $amountPerStudent = floatval($data['amount_per_student']);
                    
                    if ($amountPerStudent <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Valid payment amount is required'
                        ], 400);
                    }
                    
                    $amount = $studentCount * $amountPerStudent;
                    $serviceName = $data['service_name'] ?? 'Other Service';
                    $description = "{$serviceName}: {$studentCount} student(s)" . (isset($data['description']) && !empty($data['description']) ? ' - ' . $data['description'] : '');
                } 
                // Legacy format (single student)
                else {
                    if (empty($data['amount']) || $data['amount'] <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Valid payment amount is required'
                        ], 400);
                    }
                    
                    if (empty($data['service'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a service type'
                        ], 400);
                    }
                    
                    $amount = $data['amount'];
                    $serviceName = ucwords(str_replace('_', ' ', $data['service']));
                    $description = "Payment for {$serviceName}" . (isset($data['description']) && !empty($data['description']) ? ': ' . $data['description'] : '');
                }
            } elseif ($type === 'admission_application') {
                // Admission application payment
                if (empty($data['application_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Application ID is required'
                    ], 400);
                }

                if (empty($data['amount']) || $data['amount'] <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Valid payment amount is required'
                    ], 400);
                }

                $amount = $data['amount'];
                $description = 'Admission Application Fee - ' . ($data['application_number'] ?? '');
            }
            
            // Final validation of amount
            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment amount: ₦' . number_format($amount, 2)
                ], 400);
            }
            
            // Generate order ID
            $orderId = 'SS_' . strtoupper(uniqid());
            
            // Log payment initiation
            Log::info('Payment Initiation', [
                'type' => $type,
                'amount' => $amount,
                'orderId' => $orderId,
                'user' => $user->name,
                'description' => $description
            ]);
            
            // Generate Remita RRR
            $rrr = $this->generateRRR([
                'amount' => $amount,
                'orderId' => $orderId,
                'payerName' => $user->name,
                'payerEmail' => $user->email,
                'payerPhone' => $user->phone,
                'description' => $description,
            ]);
            
            if (!$rrr['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $rrr['message'] ?? 'Failed to generate RRR'
                ], 400);
            }
            
            // Create or update invoice record for this payment
            $invoice = null;
            
            if ($type === 'invoices' && !empty($invoiceIds)) {
                // For existing invoices, update ALL of them with RRR and metadata
                // Link them together with the same RRR
                $invoiceMetadata = [
                    'type' => $type,
                    'data' => $data,
                    'orderId' => $orderId,
                    'payment_method' => 'Remita',
                    'RRR' => $rrr['RRR'],
                    'invoice_ids' => $invoiceIds, // Store all invoice IDs
                    'total_invoices' => count($invoiceIds)
                ];
                
                // Update all selected invoices with the same RRR
                foreach ($invoiceIds as $invoiceId) {
                    $invoiceToUpdate = Invoice::find($invoiceId);
                    if ($invoiceToUpdate) {
                        $invoiceToUpdate->update([
                            'invoice_number' => $rrr['RRR'], // All invoices share same RRR
                            'status' => 'Pending',
                            'metadata' => json_encode($invoiceMetadata)
                        ]);
                    }
                }
                
                // Use first invoice for reference
                $invoice = Invoice::find($invoiceIds[0]);
            } elseif ($type === 'school_fees' && !empty($data['students'])) {
                // For school fees, create separate invoices for EACH student, term, and session combination
                $createdInvoices = [];
                
                foreach ($data['students'] as $studentId) {
                    $student = Student::with('classArm.schoolClass')->find($studentId);
                    if (!$student) continue;
                    
                    $classLevel = $student->classArm->schoolClass->level ?? null;
                    if (!$classLevel) continue;
                    
                    foreach ($data['sessions'] as $sessionNameOrId) {
                        // Get or create session
                        $session = is_numeric($sessionNameOrId) 
                            ? AcademicSession::find($sessionNameOrId)
                            : AcademicSession::where('name', $sessionNameOrId)->first();
                        
                        if (!$session) {
                            // Create session if it doesn't exist
                            $session = AcademicSession::create([
                                'name' => $sessionNameOrId,
                                'start_date' => now(),
                                'end_date' => now()->addYear(),
                                'is_current' => false,
                            ]);
                            Log::info('Created academic session', ['session' => $session->name]);
                        }
                        
                        foreach ($data['terms'] as $termName) {
                            $termMapping = [
                                '1st term' => '1st Term',
                                '2nd term' => '2nd Term',
                                '3rd term' => '3rd Term',
                            ];
                            $mappedTerm = $termMapping[strtolower($termName)] ?? $termName;
                            
                            // Get or create term
                            $term = Term::where('name', $mappedTerm)->first();
                            if (!$term) {
                                // Create term if it doesn't exist
                                $term = Term::create([
                                    'name' => $mappedTerm,
                                    'start_date' => now(),
                                    'end_date' => now()->addMonths(4),
                                    'is_current' => false,
                                ]);
                                Log::info('Created term', ['term' => $term->name]);
                            }
                            
                            $paymentSetup = PaymentSetup::schoolFeeFor($classLevel, $mappedTerm);
                            
                            if (!$paymentSetup) continue;
                            
                            // Prepare metadata for this invoice
                            $invoiceMetadata = [
                                'type' => $type,
                                'data' => $data,
                                'orderId' => $orderId,
                                'payment_method' => 'Remita',
                                'RRR' => $rrr['RRR']
                            ];
                            
                            // Create invoice for this specific student, session, and term
                            $newInvoice = Invoice::create([
                                'invoice_number' => $rrr['RRR'],
                                'student_id' => $studentId,
                                'academic_session_id' => $session->id,
                                'term_id' => $term->id,
                                'total_amount' => $paymentSetup->amount,
                                'amount_paid' => 0,
                                'balance' => $paymentSetup->amount,
                                'due_date' => now()->addDays(30),
                                'status' => 'Pending',
                                'notes' => "School Fees - {$student->user->name} - {$session->name}, {$term->name}",
                                'metadata' => json_encode($invoiceMetadata),
                            ]);
                            
                            // Create invoice item
                            DB::table('invoice_items')->insert([
                                'invoice_id' => $newInvoice->id,
                                'payment_setup_id' => $paymentSetup->id,
                                'amount' => $paymentSetup->amount,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            $createdInvoices[] = $newInvoice->id;
                            
                            Log::info('Invoice created for school fees', [
                                'invoice_id' => $newInvoice->id,
                                'student' => $student->user->name,
                                'session' => $session->name,
                                'term' => $term->name,
                                'amount' => $paymentSetup->amount
                            ]);
                        }
                    }
                }
                
                // Use first invoice for reference
                $invoice = !empty($createdInvoices) ? Invoice::find($createdInvoices[0]) : null;
                $invoiceIds = $createdInvoices;
                
            } elseif ($type === 'other_services' && !empty($data['students'])) {
                // For other services, create separate invoices for EACH student
                $createdInvoices = [];
                
                // Get current academic session and term from SessionTerm (source of truth)
                $currentSessionTerm = SessionTerm::where('is_current', true)->first();
                $currentSession = $currentSessionTerm 
                    ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
                    : AcademicSession::where('is_current', true)->first();
                $currentTerm = $currentSessionTerm 
                    ? Term::where('name', $currentSessionTerm->term_name)->first()
                    : Term::where('is_current', true)->first();
                
                // Fallback to first session/term if no current ones
                if (!$currentSession) {
                    $currentSession = AcademicSession::first();
                }
                if (!$currentTerm) {
                    $currentTerm = Term::first();
                }
                
                // Ensure we have session and term
                if (!$currentSession || !$currentTerm) {
                    throw new \Exception('No academic session or term found. Please configure academic settings.');
                }
                
                // Get the payment setup for this service (if not custom)
                $paymentSetup = null;
                if (!empty($data['is_custom']) && $data['service_id'] === 'custom') {
                    // For custom services, use any non-School Fees setup as a placeholder
                    $paymentSetup = PaymentSetup::where('payment_type', '!=', 'School Fees')
                        ->where('status', 'Active')
                        ->first();
                    
                    if (!$paymentSetup) {
                        throw new \Exception('No payment setup found. Please contact administrator to configure at least one service in payment setup.');
                    }
                } else {
                    $paymentSetup = PaymentSetup::find($data['service_id']);
                    
                    if (!$paymentSetup) {
                        throw new \Exception('Service not found. Please contact administrator.');
                    }
                }
                
                $amountPerStudent = $data['amount_per_student'];
                
                foreach ($data['students'] as $studentId) {
                    $student = Student::with('user')->find($studentId);
                    if (!$student) continue;
                    
                    // Prepare metadata for this invoice
                    $invoiceMetadata = [
                        'type' => $type,
                        'data' => $data,
                        'orderId' => $orderId,
                        'payment_method' => 'Remita',
                        'RRR' => $rrr['RRR'],
                        'service_name' => $data['service_name'] ?? $paymentSetup->payment_type
                    ];
                    
                    // Create invoice for this student
                    $newInvoice = Invoice::create([
                        'invoice_number' => $rrr['RRR'],
                        'student_id' => $studentId,
                        'academic_session_id' => $currentSession->id,
                        'term_id' => $currentTerm->id,
                        'total_amount' => $amountPerStudent,
                        'amount_paid' => 0,
                        'balance' => $amountPerStudent,
                        'due_date' => now()->addDays(30),
                        'status' => 'Pending',
                        'notes' => ($data['service_name'] ?? $paymentSetup->payment_type) . ' - ' . $student->user->name . ($data['description'] ? ' - ' . $data['description'] : ''),
                        'metadata' => json_encode($invoiceMetadata),
                    ]);
                    
                    // Create invoice item
                    DB::table('invoice_items')->insert([
                        'invoice_id' => $newInvoice->id,
                        'payment_setup_id' => $paymentSetup->id,
                        'amount' => $amountPerStudent,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $createdInvoices[] = $newInvoice->id;
                    
                    Log::info('Invoice created for other service', [
                        'invoice_id' => $newInvoice->id,
                        'student' => $student->user->name,
                        'service' => $data['service_name'] ?? $paymentSetup->payment_type,
                        'amount' => $amountPerStudent
                    ]);
                }
                
                // Use first invoice for reference
                $invoice = !empty($createdInvoices) ? Invoice::find($createdInvoices[0]) : null;
                $invoiceIds = $createdInvoices;
            } elseif ($type === 'admission_application') {
                // Admission applications now handled entirely in AdmissionApplicationController
                // This endpoint should not be called for admission applications
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment type. Admission applications are processed differently.'
                ], 400);
            } else {
                // Fallback for legacy single student format
                $invoice = null;
                $invoiceIds = [];
            }
            
            // Store temporary data in session for verification
            session([
                'remita_payment_' . $rrr['RRR'] => [
                    'type' => $type,
                    'data' => $data,
                    'orderId' => $orderId,
                    'amount' => $amount,
                    'invoice_id' => $invoice ? $invoice->id : null,
                    'invoice_ids' => !empty($invoiceIds) ? $invoiceIds : [$invoice ? $invoice->id : null], // Store all invoice IDs
                    'application_id' => $type === 'admission_application' ? ($data['application_id'] ?? null) : null,
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'rrr' => $rrr['RRR'],
                'amount' => $amount,
                'orderId' => $orderId,
                'merchantId' => config('remita.merchant_id'),
                'publicKey' => config('remita.public_key'),
                'invoiceId' => $invoice ? $invoice->id : null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Remita initiation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }
    
    /**
     * Generate Remita RRR
     */
    private function generateRRR($data)
    {
        try {
            $merchantId = config('remita.merchant_id');
            $serviceTypeId = config('remita.service_type_id');
            $apiKey = config('remita.api_key');
            $gatewayUrl = config('remita.gateway_url');
            
            // Validate required config
            if (empty($merchantId)) {
                Log::error('RRR Generation: REMITA_MERCHANT_ID not configured');
                return [
                    'success' => false,
                    'message' => 'Remita Merchant ID not configured. Please contact administrator.'
                ];
            }
            
            if (empty($serviceTypeId)) {
                Log::error('RRR Generation: REMITA_SERVICE_TYPE_ID not configured');
                return [
                    'success' => false,
                    'message' => 'Remita Service Type ID not configured. Please contact administrator.'
                ];
            }
            
            if (empty($apiKey)) {
                Log::error('RRR Generation: REMITA_API_KEY not configured');
                return [
                    'success' => false,
                    'message' => 'Remita API Key not configured. Please contact administrator.'
                ];
            }
            
            // Validate required payload data
            $missingFields = [];
            
            if (empty($data['amount']) || $data['amount'] <= 0) {
                $missingFields[] = 'amount';
            }
            
            if (empty($data['orderId'])) {
                $missingFields[] = 'orderId';
            }
            
            if (empty($data['payerName'])) {
                $missingFields[] = 'payerName';
            }
            
            if (empty($data['payerEmail'])) {
                $missingFields[] = 'payerEmail';
            }
            
            if (empty($data['payerPhone'])) {
                $missingFields[] = 'payerPhone';
            }
            
            if (empty($data['description'])) {
                $missingFields[] = 'description';
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
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verify Remita payment status
     */
    public function verifyRemita(Request $request)
    {
        try {
            $rrr = $request->rrr;
            $orderId = $request->orderId;
            
            Log::info('Payment verification started', [
                'RRR' => $rrr,
                'orderId' => $orderId
            ]);
            
            $merchantId = config('remita.merchant_id');
            $apiKey = config('remita.api_key');
            
            // Use the correct endpoint format from working TypeScript implementation
            $baseUrl = config('remita.demo_mode') 
                ? 'https://demo.remita.net' 
                : 'https://login.remita.net';
            
            // Hash calculation matches working implementation: rrr + apiKey + merchantId
            $hash = hash('sha512', $rrr . $apiKey . $merchantId);
            
            // Correct URL format: /remita/exapp/api/v1/send/api/echannelsvc/{merchantId}/{rrr}/{hash}/status.reg
            $statusCheckUrl = $baseUrl . '/remita/exapp/api/v1/send/api/echannelsvc/' . $merchantId . '/' . $rrr . '/' . $hash . '/status.reg';
            
            Log::info('Remita verification API call', [
                'url' => $statusCheckUrl,
                'merchantId' => $merchantId
            ]);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $hash,
            ])->get($statusCheckUrl);
            
            Log::info('Remita verification response', [
                'status_code' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Remita verification result', [
                    'result' => $result
                ]);
                
                // Get session data for this RRR
                $paymentData = session('remita_payment_' . $rrr);
                
                // If no session data, find invoice by RRR (for pending invoice payments)
                if (!$paymentData) {
                    Log::info('No session data, looking up invoice by RRR');
                    $invoice = Invoice::where('invoice_number', $rrr)->first();
                    if ($invoice) {
                        Log::info('Found invoice for RRR', ['invoice_id' => $invoice->id]);
                        $paymentData = [
                            'invoice_id' => $invoice->id,
                            'amount' => $invoice->balance ?? $invoice->total_amount,
                            'type' => 'invoice_retry'
                        ];
                    } else {
                        Log::warning('No invoice found for RRR: ' . $rrr);
                    }
                }
                
                // Check both 'status' and 'statuscode' fields (Remita uses both)
                $statusCode = $result['status'] ?? $result['statuscode'] ?? null;
                
                Log::info('Payment verification data', [
                    'paymentData' => $paymentData,
                    'status' => $statusCode ?? 'unknown'
                ]);
                
                if ($statusCode && ($statusCode == '00' || $statusCode == '01')) {
                    // Payment successful - handle ALL invoices
                    $invoiceIds = [];
                    
                    // Get all invoice IDs from payment data
                    if ($paymentData && isset($paymentData['invoice_ids'])) {
                        $invoiceIds = $paymentData['invoice_ids'];
                    } elseif ($paymentData && isset($paymentData['invoice_id'])) {
                        $invoiceIds = [$paymentData['invoice_id']];
                    }
                    
                    $totalAmount = $result['amount'] ?? ($paymentData['amount'] ?? 0);
                    $paymentRecords = [];
                    
                    // Process each invoice
                    foreach ($invoiceIds as $invoiceId) {
                        $invoice = Invoice::find($invoiceId);
                        if (!$invoice) continue;
                        
                        // Calculate amount for this invoice (proportional to invoice total)
                        $invoiceAmount = $invoice->balance ?? $invoice->total_amount;
                        
                        // Create payment record for this invoice
                        $payment = Payment::create([
                            'invoice_id' => $invoice->id,
                            'payment_reference' => $rrr,
                            'amount' => $invoiceAmount,
                            'payment_date' => now(),
                            'payment_method' => 'Remita',
                            'transaction_id' => $orderId,
                            'notes' => 'Remita payment - ' . ($paymentData['type'] ?? 'payment') . ' (Multi-invoice: ' . count($invoiceIds) . ' invoices)',
                        ]);
                        
                        $paymentRecords[] = $payment->id;
                        
                        // Update invoice status
                        $invoice->update([
                            'status' => 'Paid',
                            'amount_paid' => $invoiceAmount,
                            'balance' => 0,
                        ]);
                        
                        Log::info('Invoice updated', [
                            'invoice_id' => $invoice->id,
                            'student_id' => $invoice->student_id,
                            'amount' => $invoiceAmount,
                            'rrr' => $rrr
                        ]);
                    }
                    
                    // Handle admission application payment
                    if ($paymentData && $paymentData['type'] === 'admission_application') {
                        $application = \App\Models\AdmissionApplication::find($paymentData['application_id']);
                        if ($application) {
                            // Update application status to Draft
                            $application->update(['status' => 'Draft']);
                            
                            Log::info('Admission application updated after payment', [
                                'application_id' => $application->id,
                                'amount' => $paymentData['amount'],
                                'rrr' => $rrr
                            ]);
                            
                            // Clear session data
                            session()->forget('remita_payment_' . $rrr);
                            
                            return response()->json([
                                'success' => true,
                                'message' => 'Payment verified successfully',
                                'status' => $statusCode,
                                'redirect' => route('parent.admission.form', $application->id)
                            ]);
                        }
                    }
                    
                    // Clear session data
                    session()->forget('remita_payment_' . $rrr);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment verified successfully for ' . count($invoiceIds) . ' invoice(s)',
                        'status' => $statusCode,
                        'payment_ids' => $paymentRecords,
                        'invoices_count' => count($invoiceIds),
                    ]);
                }
                
                // Handle different payment statuses
                if ($statusCode == '021') {
                    // Transaction is pending (not yet completed)
                    return response()->json([
                        'success' => false,
                        'pending' => true,
                        'message' => 'Payment is still pending. Please complete the payment or try again later.',
                        'status' => $statusCode,
                    ]);
                }
                
                // Payment failed or other status
                $statusMessages = [
                    '020' => 'Payment not found',
                    '022' => 'Payment cancelled',
                    '023' => 'Payment expired',
                    '024' => 'Payment declined',
                ];
                
                $message = $statusMessages[$statusCode] ?? 'Payment verification failed';
                if (isset($result['message'])) {
                    $message .= ': ' . $result['message'];
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'status' => $statusCode ?? 'unknown',
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment - HTTP status: ' . $response->status()
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Remita verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying payment'
            ], 500);
        }
    }
    
    /**
     * Verify payment status for invoices paid outside the portal
     */
    public function verifyStatus(Request $request)
    {
        try {
            $invoiceId = $request->invoice_id;
            $rrr = $request->rrr;
            
            Log::info('Manual payment status verification', [
                'invoice_id' => $invoiceId,
                'rrr' => $rrr
            ]);
            
            // Find the invoice
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }
            
            // Check if invoice belongs to user's dependent
            $user = Auth::user();
            $dependentIds = $user->dependents()->pluck('students.id');
            
            if (!$dependentIds->contains($invoice->student_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            // Query Remita to check payment status
            $merchantId = config('remita.merchant_id');
            $apiKey = config('remita.api_key');
            
            // Use the correct endpoint format from working TypeScript implementation
            $baseUrl = config('remita.demo_mode') 
                ? 'https://demo.remita.net' 
                : 'https://login.remita.net';
            
            // Hash calculation matches working implementation: rrr + apiKey + merchantId
            $hash = hash('sha512', $rrr . $apiKey . $merchantId);
            
            // Correct URL format: /remita/exapp/api/v1/send/api/echannelsvc/{merchantId}/{rrr}/{hash}/status.reg
            $statusCheckUrl = $baseUrl . '/remita/exapp/api/v1/send/api/echannelsvc/' . $merchantId . '/' . $rrr . '/' . $hash . '/status.reg';
            
            Log::info('Checking Remita payment status', [
                'rrr' => $rrr,
                'invoice_id' => $invoiceId,
                'url' => $statusCheckUrl
            ]);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $hash,
            ])->get($statusCheckUrl);
            
            Log::info('Remita status check response', [
                'status_code' => $response->status(),
                'body' => $response->body(),
                'url' => $statusCheckUrl
            ]);
            
            // Handle both successful and error responses from Remita
            if ($response->status() === 200 || $response->status() === 404) {
                $result = $response->json();
                
                Log::info('Remita verification result parsed', [
                    'result' => $result
                ]);
                
                // Check if responseCode exists (Remita's error structure)
                if (isset($result['responseCode']) && $result['responseCode'] == 404) {
                    // Payment not found in Remita
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment not found in Remita system',
                        'remita_status' => 'Payment with this RRR has not been made yet',
                        'status_code' => '404',
                        'details' => 'This RRR has not been used for any payment. Please complete payment at the bank or use the Pay Now button.'
                    ]);
                }
                
                // Check if payment was successful (Remita success response)
                // Check both 'status' and 'statuscode' fields (Remita uses both)
                $statusCode = $result['status'] ?? $result['statuscode'] ?? null;
                
                if ($statusCode && ($statusCode == '00' || $statusCode == '01')) {
                    // Payment confirmed - update ALL invoices with this RRR
                    
                    DB::beginTransaction();
                    
                    try {
                        // Find ALL invoices with this RRR (in case of multi-invoice payment)
                        $allInvoices = Invoice::where('invoice_number', $rrr)->get();
                        $updatedCount = 0;
                        
                        foreach ($allInvoices as $invoiceToUpdate) {
                            // Check if payment record already exists for this invoice
                            $existingPayment = Payment::where('payment_reference', $rrr)
                                ->where('invoice_id', $invoiceToUpdate->id)
                                ->first();
                            
                            if (!$existingPayment) {
                                $invoiceAmount = $invoiceToUpdate->balance ?? $invoiceToUpdate->total_amount;
                                
                                // Create payment record
                                $payment = Payment::create([
                                    'invoice_id' => $invoiceToUpdate->id,
                                    'payment_reference' => $rrr,
                                    'amount' => $invoiceAmount,
                                    'payment_date' => now(),
                                    'payment_method' => 'Remita',
                                    'transaction_id' => $rrr,
                                    'notes' => 'Payment verified manually - paid at bank/other channel' . ($allInvoices->count() > 1 ? ' (Multi-invoice: ' . $allInvoices->count() . ' invoices)' : ''),
                                ]);
                                
                                // Update invoice status
                                $invoiceToUpdate->update([
                                    'status' => 'Paid',
                                    'amount_paid' => $payment->amount,
                                    'balance' => 0,
                                ]);
                                
                                $updatedCount++;
                                
                                Log::info('Payment record created from manual verification', [
                                    'payment_id' => $payment->id,
                                    'invoice_id' => $invoiceToUpdate->id
                                ]);
                            } else {
                                // Payment already exists, just update invoice if needed
                                if ($invoiceToUpdate->status !== 'Paid') {
                                    $invoiceToUpdate->update([
                                        'status' => 'Paid',
                                        'amount_paid' => $existingPayment->amount,
                                        'balance' => 0,
                                    ]);
                                    $updatedCount++;
                                }
                            }
                        }
                        
                        DB::commit();
                        
                        $message = $allInvoices->count() > 1 
                            ? "Payment verified successfully for {$allInvoices->count()} invoice(s)"
                            : 'Payment verified and updated successfully';
                        
                        return response()->json([
                            'success' => true,
                            'message' => $message,
                            'rrr' => $rrr,
                            'amount' => $result['amount'] ?? $invoice->total_amount,
                            'status' => 'Paid',
                            'remita_status' => $result['status'],
                            'invoices_updated' => $allInvoices->count()
                        ]);
                        
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error updating payment record: ' . $e->getMessage());
                        throw $e;
                    }
                } else {
                    // Payment not successful or still pending
                    $statusMessage = $result['statusMessage'] ?? $result['message'] ?? 'Payment not found or still pending';
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment not confirmed by Remita',
                        'remita_status' => $statusMessage,
                        'status_code' => $statusCode ?? 'unknown'
                    ]);
                }
            } else {
                // HTTP request failed
                Log::error('Remita API request failed', [
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                    'rrr' => $rrr
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Remita. HTTP Status: ' . $response->status(),
                    'remita_status' => $response->body(),
                    'error_details' => 'Please check your internet connection or contact support if this persists.'
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify payment status with Remita',
                'remita_status' => 'Connection failed'
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Payment status verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying payment status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate school fees amount before payment
     */
    public function calculateSchoolFeesAmount(Request $request)
    {
        try {
            $data = $request->all();
            
            // Validate input
            if (empty($data['students']) || empty($data['terms']) || empty($data['sessions'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select students, terms, and sessions'
                ], 400);
            }
            
            $studentCount = count($data['students']);
            $termCount = count($data['terms']);
            $sessionCount = count($data['sessions']);
            
            // Check for existing invoices first (only for existing sessions/terms)
            $existingInvoices = [];
            foreach ($data['students'] as $studentId) {
                foreach ($data['sessions'] as $sessionNameOrId) {
                    // Try to find session by name or ID
                    $session = is_numeric($sessionNameOrId) 
                        ? AcademicSession::find($sessionNameOrId)
                        : AcademicSession::where('name', $sessionNameOrId)->first();
                    
                    if (!$session) {
                        // Session doesn't exist yet, will be auto-created - skip duplicate check
                        continue;
                    }
                    
                    foreach ($data['terms'] as $termName) {
                        // Get term ID from term name
                        $termMapping = [
                            '1st term' => '1st Term',
                            '2nd term' => '2nd Term',
                            '3rd term' => '3rd Term',
                        ];
                        $mappedTerm = $termMapping[strtolower($termName)] ?? $termName;
                        
                        $term = Term::where('name', $mappedTerm)->first();
                        if (!$term) {
                            // Term doesn't exist yet, will be auto-created - skip duplicate check
                            continue;
                        }
                        
                        // Check if invoice already exists for this combination
                        $existingInvoice = Invoice::where('student_id', $studentId)
                            ->where('academic_session_id', $session->id)
                            ->where('term_id', $term->id)
                            ->whereIn('status', ['Pending', 'Partial', 'Paid'])
                            ->first();
                        
                        if ($existingInvoice) {
                            $student = Student::with('user')->find($studentId);
                            $existingInvoices[] = [
                                'student' => $student->user->name ?? 'Unknown',
                                'session' => $session->name,
                                'term' => $term->name,
                                'invoice_number' => $existingInvoice->invoice_number,
                                'status' => $existingInvoice->status
                            ];
                        }
                    }
                }
            }
            
            // If any existing invoices found, return them immediately
            if (!empty($existingInvoices)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate invoices detected',
                    'existing_invoices' => $existingInvoices
                ], 400);
            }
            
            // Fetch fee amounts from payment_setups table
            $missingFees = [];
            $totalAmount = 0;
            $breakdown = [];
            
            // Loop through each student to get their specific level
            foreach ($data['students'] as $studentId) {
                $student = Student::with('classArm.schoolClass')->find($studentId);
                
                if (!$student) {
                    continue;
                }
                
                // Get student's class level (e.g., JSS 1, SS 2)
                $classLevel = $student->classArm->schoolClass->level ?? null;
                
                if (!$classLevel) {
                    Log::warning('Student has no class assigned', ['student_id' => $studentId]);
                    continue;
                }
                
                $studentAmount = 0;
                
                // Loop through each term and session for calculation
                foreach ($data['terms'] as $termName) {
                    foreach ($data['sessions'] as $sessionNameOrId) {
                        $paymentSetup = PaymentSetup::schoolFeeFor($classLevel, $termName);
                        
                        if ($paymentSetup) {
                            $studentAmount += $paymentSetup->amount;
                            $totalAmount += $paymentSetup->amount;
                        } else {
                            $missingFees[] = [
                                'student' => $student->user->name ?? 'Unknown',
                                'level' => $classLevel,
                            ];
                        }
                    }
                }
                
                if ($studentAmount > 0) {
                    $breakdown[] = [
                        'student' => $student->user->name ?? 'Unknown',
                        'class' => $classLevel,
                        'amount' => $studentAmount
                    ];
                }
            }
            
            // If any fees are missing, return error
            if (!empty($missingFees)) {
                $errorMessage = "Fee setup not configured for:\n";
                foreach ($missingFees as $missing) {
                    $errorMessage .= "- {$missing['student']} ({$missing['level']}) - {$missing['session']}, {$missing['term']}\n";
                }
                $errorMessage .= "\nPlease contact the school administrator to configure fee amounts.";
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'missing_fees' => $missingFees
                ], 400);
            }
            
            if ($totalAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not calculate payment amount. Please ensure fee setup is configured properly.'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'amount' => $totalAmount,
                'breakdown' => $breakdown,
                'summary' => [
                    'students' => $studentCount,
                    'terms' => $termCount,
                    'sessions' => $sessionCount,
                    'total_payments' => $studentCount * $termCount * $sessionCount
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Calculate school fees error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating fees'
            ], 500);
        }
    }
}
