<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\PaymentSetup;
use App\Models\SchoolSettings;
use App\Models\SessionTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('student.payments.index', [
                'payments' => ['pending' => collect(), 'history' => collect()],
                'summary' => ['total_pending' => 0, 'pending_count' => 0, 'total_paid' => 0],
                'sessions' => collect(),
                'terms' => collect(),
                'filterSession' => null,
                'filterTerm' => null,
            ]);
        }

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

        $filterSession = $request->input('session');
        $filterTerm = $request->input('term');

        // Build pending query
        $pendingQuery = Invoice::where('student_id', $student->id)
            ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
            ->with(['term', 'academicSession', 'invoiceItems.feeSetup']);

        if ($filterSession) $pendingQuery->where('academic_session_id', $filterSession);
        if ($filterTerm) $pendingQuery->where('term_id', $filterTerm);

        // Build history query
        $historyQuery = Invoice::where('student_id', $student->id)
            ->where('status', 'Paid')
            ->with(['term', 'academicSession', 'invoiceItems.feeSetup', 'payments']);

        if ($filterSession) $historyQuery->where('academic_session_id', $filterSession);
        if ($filterTerm) $historyQuery->where('term_id', $filterTerm);

        $payments = [
            'pending' => $pendingQuery->latest()->get(),
            'history' => $historyQuery->latest()->get(),
        ];

        $summary = [
            'total_pending' => Invoice::where('student_id', $student->id)
                ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
                ->sum('balance'),
            'pending_count' => Invoice::where('student_id', $student->id)
                ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
                ->count(),
            'total_paid' => Invoice::where('student_id', $student->id)
                ->where('status', 'Paid')
                ->sum('amount_paid'),
        ];

        return view('student.payments.index', compact(
            'payments', 'summary', 'sessions', 'terms', 'filterSession', 'filterTerm'
        ));
    }

    public function show($id)
    {
        $user = Auth::user();
        $student = $user->student;

        $invoice = Invoice::where('student_id', $student->id)
            ->with(['payments', 'invoiceItems.feeSetup', 'term', 'academicSession'])
            ->findOrFail($id);

        return view('student.payments.show', compact('invoice'));
    }

    public function downloadReceipt($id)
    {
        $user = Auth::user();
        $student = $user->student;

        $invoice = Invoice::where('student_id', $student->id)
            ->with(['student.user', 'student.classArm.schoolClass', 'term', 'academicSession', 'invoiceItems.feeSetup', 'payments'])
            ->findOrFail($id);

        if ($invoice->status !== 'Paid') {
            return back()->with('error', 'Receipt can only be downloaded for paid invoices.');
        }

        $payment = $invoice->payments()->latest()->first();
        $schoolSettings = SchoolSettings::first();

        $pdf = Pdf::loadView('parent.payments.receipt', compact('invoice', 'payment', 'schoolSettings'));
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'Receipt_' . $invoice->invoice_number . '_' . date('Y-m-d') . '.pdf';
        return $pdf->stream($fileName);
    }

    public function initiateRemita(Request $request)
    {
        try {
            $user = Auth::user();
            $student = $user->student;

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student record not found.'], 400);
            }

            if (empty($user->email)) {
                return response()->json(['success' => false, 'message' => 'Email address is required. Please update your profile.'], 400);
            }

            // Validate Remita configuration
            $merchantId = config('remita.merchant_id');
            $apiKey = config('remita.api_key');
            $serviceTypeId = config('remita.service_type_id');

            if (empty($merchantId) || empty($apiKey) || empty($serviceTypeId)) {
                Log::error('Remita configuration incomplete', [
                    'has_merchant_id' => !empty($merchantId),
                    'has_api_key' => !empty($apiKey),
                    'has_service_type_id' => !empty($serviceTypeId),
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Payment gateway is not configured. Please contact the school administrator to set up Remita credentials in the .env file (REMITA_MERCHANT_ID, REMITA_API_KEY, REMITA_SERVICE_TYPE_ID).'
                ], 400);
            }

            $invoiceIds = $request->input('invoice_ids', []);
            if (empty($invoiceIds)) {
                return response()->json(['success' => false, 'message' => 'No invoices selected for payment.'], 400);
            }

            $invoices = Invoice::where('student_id', $student->id)
                ->whereIn('id', $invoiceIds)
                ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
                ->get();

            if ($invoices->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No valid invoices found.'], 400);
            }

            $amount = $invoices->sum(function ($invoice) {
                return $invoice->balance ?? $invoice->total_amount;
            });

            $description = 'Student Payment - ' . $student->first_name . ' ' . $student->surname . ' (' . $invoices->count() . ' invoice(s))';
            $orderId = 'STU-' . time() . '-' . $student->id;

            $hash = hash('sha512', $merchantId . $serviceTypeId . $orderId . $amount . $apiKey);

            $payload = [
                'serviceTypeId' => $serviceTypeId,
                'amount' => $amount,
                'orderId' => $orderId,
                'payerName' => $user->name,
                'payerEmail' => $user->email,
                'payerPhone' => $user->phone ?? '',
                'description' => $description,
            ];

            // Store payment reference in metadata and session
            $invoiceIdList = [];
            foreach ($invoices as $invoice) {
                $meta = json_decode($invoice->metadata ?? '{}', true);
                $meta['remita_order_id'] = $orderId;
                $invoice->update(['metadata' => json_encode($meta)]);
                $invoiceIdList[] = $invoice->id;
            }

            // Store in session for verification
            session(['remita_payment_student_' . $orderId => [
                'invoice_ids' => $invoiceIdList,
                'amount' => $amount,
                'student_id' => $student->id,
                'type' => 'student_payment',
            ]]);

            Log::info('Student Remita payment initiated', [
                'student_id' => $student->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'invoices' => $invoiceIdList,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'merchantId' => $merchantId,
                    'hash' => $hash,
                    'payload' => $payload,
                    'invoiceIds' => $invoiceIds,
                    'amount' => $amount,
                    'orderId' => $orderId,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Student Remita payment initiation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    public function verifyRemita(Request $request)
    {
        try {
            $user = Auth::user();
            $student = $user->student;

            $orderId = $request->input('orderId');
            $rrr = $request->input('rrr');

            Log::info('Student payment verification started', [
                'student_id' => $student->id,
                'orderId' => $orderId,
                'rrr' => $rrr,
            ]);

            // Verify with Remita API
            $merchantId = config('remita.merchant_id');
            $apiKey = config('remita.api_key');

            $baseUrl = config('remita.demo_mode') 
                ? 'https://demo.remita.net' 
                : 'https://login.remita.net';

            $hash = hash('sha512', $rrr . $apiKey . $merchantId);
            $statusCheckUrl = $baseUrl . '/remita/exapp/api/v1/send/api/echannelsvc/' . $merchantId . '/' . $rrr . '/' . $hash . '/status.reg';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $hash,
            ])->get($statusCheckUrl);

            Log::info('Student Remita verification response', [
                'status_code' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $statusCode = $result['status'] ?? $result['statuscode'] ?? null;

                if ($statusCode && ($statusCode == '00' || $statusCode == '01')) {
                    // Payment successful - find invoices by orderId in metadata
                    $invoices = Invoice::where('student_id', $student->id)
                        ->where('metadata->remita_order_id', $orderId)
                        ->get();

                    if ($invoices->isEmpty()) {
                        // Try session data
                        $sessionData = session('remita_payment_student_' . $orderId);
                        if ($sessionData && !empty($sessionData['invoice_ids'])) {
                            $invoices = Invoice::whereIn('id', $sessionData['invoice_ids'])->get();
                        }
                    }

                    $paymentRecords = [];
                    foreach ($invoices as $invoice) {
                        $invoiceAmount = $invoice->balance ?? $invoice->total_amount;

                        // Create payment record
                        \App\Models\Payment::create([
                            'invoice_id' => $invoice->id,
                            'payment_reference' => $rrr,
                            'amount' => $invoiceAmount,
                            'payment_date' => now(),
                            'payment_method' => 'Remita',
                            'transaction_id' => $orderId,
                            'notes' => 'Student Remita online payment (' . $invoices->count() . ' invoice(s))',
                            'status' => 'Completed',
                        ]);

                        // Update invoice status
                        $invoice->update([
                            'status' => 'Paid',
                            'amount_paid' => $invoice->total_amount,
                            'balance' => 0,
                        ]);

                        $paymentRecords[] = $invoice->id;
                    }

                    // Clear session
                    session()->forget('remita_payment_student_' . $orderId);

                    Log::info('Student payment verified successfully', [
                        'student_id' => $student->id,
                        'invoices_paid' => $paymentRecords,
                        'rrr' => $rrr,
                    ]);

                    return response()->json([
                        'success' => true, 
                        'message' => 'Payment verified successfully! ' . count($paymentRecords) . ' invoice(s) paid.',
                    ]);
                }

                // Payment pending or failed
                $statusMessages = [
                    '021' => 'Payment is still pending. Please complete the payment.',
                    '020' => 'Payment not found.',
                    '022' => 'Payment was cancelled.',
                    '023' => 'Payment has expired.',
                    '024' => 'Payment was declined.',
                ];

                $message = $statusMessages[$statusCode] ?? ('Payment verification returned status: ' . ($statusCode ?? 'unknown'));

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'status' => $statusCode,
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Failed to verify payment with gateway. Please try again.',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Student Remita verification failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Verification failed. Please contact the school office.'], 500);
        }
    }
}
