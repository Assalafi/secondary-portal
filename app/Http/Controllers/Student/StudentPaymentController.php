<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\PaymentSetup;
use App\Models\SchoolSettings;
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

        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        $terms = Term::orderBy('id')->get();

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

            $description = 'Student Payment for ' . $invoices->count() . ' invoice(s)';
            $orderId = 'STU-' . time() . '-' . $student->id;

            // Get Remita config
            $merchantId = config('remita.merchant_id');
            $apiKey = config('remita.api_key');
            $serviceTypeId = config('remita.service_type_id');

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

            // Store payment reference in metadata
            foreach ($invoices as $invoice) {
                $meta = json_decode($invoice->metadata ?? '{}', true);
                $meta['remita_order_id'] = $orderId;
                $invoice->update(['metadata' => json_encode($meta)]);
            }

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
            Log::error('Student Remita payment initiation failed', ['error' => $e->getMessage()]);
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

            $invoices = Invoice::where('student_id', $student->id)
                ->where('metadata->remita_order_id', $orderId)
                ->get();

            if ($invoices->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
            }

            // Mark invoices as paid
            foreach ($invoices as $invoice) {
                $invoice->update([
                    'status' => 'Paid',
                    'amount_paid' => $invoice->total_amount,
                    'balance' => 0,
                ]);

                $invoice->payments()->create([
                    'amount' => $invoice->total_amount,
                    'payment_method' => 'Remita',
                    'reference' => $rrr,
                    'status' => 'Completed',
                    'payment_date' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Payment verified successfully!']);
        } catch (\Exception $e) {
            Log::error('Student Remita verification failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Verification failed.'], 500);
        }
    }
}
