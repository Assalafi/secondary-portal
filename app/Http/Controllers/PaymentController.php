<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Student;
use App\Models\SalaryStructure;
use App\Models\PayrollRecord;
use App\Models\PaymentSetup;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Get all students for dropdown/selection
     */
    public function getStudents()
    {
        $students = Student::with(['user', 'classArm.schoolClass'])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name ?? 'Unknown',
                    'student_id' => $student->student_id,
                    'class' => optional($student->classArm->schoolClass)->name . ' ' . optional($student->classArm)->name,
                    'level' => optional($student->classArm->schoolClass)->level
                ];
            });

        return response()->json(['students' => $students]);
    }

    /**
     * Display payment overview/dashboard
     */
    public function overview()
    {
        // Calculate financial summary using Invoice and Payment tables
        $totalCollected = Invoice::where('status', 'Paid')
            ->sum('amount_paid');
        
        $outstandingFees = Invoice::whereIn('status', ['Pending', 'Partial', 'Overdue'])
            ->sum('balance');
        
        $totalPayrollExpense = PayrollRecord::where('status', 'Paid')
            ->sum('net_pay');

        // Get monthly data for charts (Income from Payments)
        $monthlyPayments = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', now()->year)
            ->groupBy('month')
            ->get();

        // Get monthly payroll expenses
        $monthlyPayroll = PayrollRecord::selectRaw('MONTH(created_at) as month, SUM(net_pay) as total')
            ->where('status', 'Paid')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->get();

        // Calculate income vs expenses for chart
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $income = $monthlyPayments->where('month', $i)->sum('total');
            $expense = $monthlyPayroll->where('month', $i)->sum('total');
            
            $chartData['income'][] = $income;
            $chartData['expenses'][] = $expense;
        }

        // Get recent payments with invoice and student details
        $recentTransactions = Payment::with([
                'invoice.student.user',
                'invoice.student.classArm.schoolClass',
                'invoice.academicSession',
                'invoice.term'
            ])
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'total_collected' => $totalCollected,
            'outstanding_fees' => $outstandingFees,
            'payroll_expense' => $totalPayrollExpense,
            'chart_data' => $chartData
        ];

        return view('admin.payments.overview', compact('stats', 'recentTransactions'));
    }

    /**
     * Display payroll generation page
     */
    public function payroll(Request $request)
    {
        $month = $request->get('month', now()->format('F'));
        $year = $request->get('year', now()->year);
        $department = $request->get('department');

        // Get staff with their salary structures
        $staffQuery = Staff::with(['user.role'])
            ->where('status', 'Active');

        if ($department) {
            $staffQuery->where('department', $department);
        }

        $staff = $staffQuery->get();

        // Get existing payroll records for the selected month/year
        $existingPayroll = PayrollRecord::where('payroll_month', $month)
            ->where('payroll_year', $year)
            ->with('staff.user')
            ->get()
            ->keyBy('staff_id');

        // Get salary structures
        $salaryStructures = SalaryStructure::where('status', 'Active')->get()->keyBy('role_level');

        return view('admin.payments.payroll', compact('staff', 'existingPayroll', 'salaryStructures', 'month', 'year'));
    }

    /**
     * Generate payroll for staff
     */
    public function generatePayroll(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer',
            'department' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $month = $request->month;
            $year = $request->year;

            // Get staff based on department filter
            $staffQuery = Staff::with(['user.role'])
                ->where('status', 'Active');

            if ($request->filled('department')) {
                $staffQuery->where('department', $request->department);
            }

            $staff = $staffQuery->get();
            $salaryStructures = SalaryStructure::where('status', 'Active')->get()->keyBy('role_level');

            $generatedCount = 0;

            foreach ($staff as $staffMember) {
                // Check if payroll already exists
                $existing = PayrollRecord::where('staff_id', $staffMember->id)
                    ->where('payroll_month', $month)
                    ->where('payroll_year', $year)
                    ->first();

                if ($existing) {
                    continue; // Skip if already generated
                }

                // Get salary structure for staff role
                $roleName = $staffMember->user->role->name ?? 'Default';
                $salaryStructure = $salaryStructures->get($roleName);

                // If exact match not found, try fallback mappings
                if (!$salaryStructure) {
                    $roleMappings = [
                        'Teacher' => 'Subject Teacher',
                        'Admin' => 'Admin Staff',
                        'Administrator' => 'Admin Staff',
                        'Default' => 'Subject Teacher'
                    ];
                    
                    $fallbackRole = $roleMappings[$roleName] ?? null;
                    if ($fallbackRole) {
                        $salaryStructure = $salaryStructures->get($fallbackRole);
                    }
                }

                if (!$salaryStructure) {
                    // Log the issue but don't stop the process
                    \Log::warning("No salary structure found for staff {$staffMember->user->name} with role: {$roleName}");
                    continue; // Skip if no salary structure found
                }

                // Calculate salary
                $basePay = $salaryStructure->base_salary;
                $allowances = $salaryStructure->allowance;
                $deductions = $salaryStructure->deduction;
                $grossPay = $basePay + $allowances;
                $netPay = $grossPay - $deductions;

                // Create payroll record
                PayrollRecord::create([
                    'staff_id' => $staffMember->id,
                    'payroll_month' => $month,
                    'payroll_year' => $year,
                    'base_pay' => $basePay,
                    'allowances' => $allowances,
                    'deductions' => $deductions,
                    'gross_pay' => $grossPay,
                    'net_pay' => $netPay,
                    'status' => 'Generated',
                    'generated_date' => now(),
                ]);

                $generatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Payroll generated for {$generatedCount} staff members"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display payment setup page
     */
    public function paymentSetup()
    {
        $paymentSetups = PaymentSetup::orderBy('created_at', 'desc')->get();
        $levels = SchoolClass::distinct()->pluck('level');
        
        return view('admin.payments.setup', compact('paymentSetups', 'levels'));
    }

    /**
     * Store new payment setup
     */
    public function storePaymentSetup(Request $request)
    {
        // Get valid levels from database
        $validLevels = SchoolClass::distinct()->pluck('level')->toArray();
        $validLevels[] = 'All'; // Add 'All' option
        
        $request->validate([
            'payment_type' => 'required|string|max:255',
            'level' => 'required|in:' . implode(',', $validLevels),
            'term' => 'required|in:All,Term 1,Term 2,Term 3',
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        PaymentSetup::create([
            'payment_type' => $request->payment_type,
            'level' => $request->level,
            'term' => $request->term,
            'amount' => $request->amount,
            'effective_date' => $request->effective_date,
            'last_updated' => now(),
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment setup created successfully'
        ]);
    }

    /**
     * Update payment setup
     */
    public function updatePaymentSetup(Request $request, PaymentSetup $paymentSetup)
    {
        // Get valid levels from database
        $validLevels = SchoolClass::distinct()->pluck('level')->toArray();
        $validLevels[] = 'All'; // Add 'All' option
        
        $request->validate([
            'payment_type' => 'required|string|max:255',
            'level' => 'required|in:' . implode(',', $validLevels),
            'term' => 'required|in:All,Term 1,Term 2,Term 3',
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        $paymentSetup->update([
            'payment_type' => $request->payment_type,
            'level' => $request->level,
            'term' => $request->term,
            'amount' => $request->amount,
            'effective_date' => $request->effective_date,
            'last_updated' => now(),
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment setup updated successfully'
        ]);
    }

    /**
     * Delete payment setup
     */
    public function deletePaymentSetup(PaymentSetup $paymentSetup)
    {
        try {
            // Check if payment setup is being used in transactions
            $transactionCount = Transaction::where('payment_type', $paymentSetup->payment_type)
                ->where('level', $paymentSetup->level)
                ->count();

            if ($transactionCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete payment setup. It is being used in transactions.'
                ], 400);
            }

            $paymentSetup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment setup deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment setup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display fees and income page
     */
    public function feesIncome(Request $request)
    {
        $level = $request->get('level');
        $termId = $request->get('term');
        $status = $request->get('status');
        $sessionId = $request->get('session');

        // Get invoices with filters
        $invoicesQuery = Invoice::with([
            'student.user',
            'student.classArm.schoolClass',
            'academicSession',
            'term',
            'payments',
            'items.paymentSetup'
        ]);

        // Filter by class level
        if ($level) {
            $invoicesQuery->whereHas('student.classArm.schoolClass', function($q) use ($level) {
                $q->where('level', $level);
            });
        }

        // Filter by term
        if ($termId) {
            $invoicesQuery->where('term_id', $termId);
        }

        // Filter by session
        if ($sessionId) {
            $invoicesQuery->where('academic_session_id', $sessionId);
        }

        // Filter by status
        if ($status) {
            $invoicesQuery->where('status', $status);
        }

        $transactions = $invoicesQuery->orderBy('created_at', 'desc')->paginate(20);

        // Calculate summary statistics
        $totalRevenue = Invoice::where('status', 'Paid')
            ->sum('amount_paid');
        
        $totalStudents = Student::count();
        
        $paidCount = Invoice::where('status', 'Paid')
            ->distinct('student_id')
            ->count('student_id');
        
        $outstandingCount = Invoice::whereIn('status', ['Pending', 'Partial', 'Overdue'])
            ->distinct('student_id')
            ->count('student_id');

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_students' => $totalStudents,
            'paid_count' => $paidCount,
            'outstanding_count' => $outstandingCount
        ];
        
        $levels = SchoolClass::distinct()->pluck('level');
        $terms = \App\Models\Term::all();
        $sessions = \App\Models\AcademicSession::orderBy('name', 'desc')->get();

        return view('admin.payments.fees-income', compact('transactions', 'stats', 'levels', 'terms', 'sessions'));
    }

    /**
     * Record new payment
     */
    public function recordPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_setup_id' => 'required|exists:payment_setups,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Get payment setup details
            $paymentSetup = PaymentSetup::findOrFail($request->payment_setup_id);
            
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'student_id' => $request->student_id,
                'academic_session_id' => $request->session_id,
                'term_id' => $request->term_id,
                'total_amount' => $request->amount,
                'amount_paid' => $request->amount,
                'balance' => 0,
                'due_date' => $request->payment_date,
                'status' => 'Paid',
                'notes' => $request->description ?? 'Manual payment - ' . $paymentSetup->payment_type,
                'metadata' => json_encode([
                    'payment_method' => $request->payment_method,
                    'recorded_by' => 'admin',
                    'manual_entry' => true,
                    'service_name' => $paymentSetup->payment_type,
                    'admin_user_id' => auth()->id()
                ])
            ]);

            // Create invoice item
            DB::table('invoice_items')->insert([
                'invoice_id' => $invoice->id,
                'payment_setup_id' => $paymentSetup->id,
                'amount' => $request->amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'transaction_id' => 'MANUAL-' . time(),
                'notes' => $request->description ?? 'Manual payment recorded by admin',
                'received_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'invoice' => $invoice,
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update invoice status
     */
    public function updateTransactionStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:Paid,Pending,Partial,Overdue',
            'remarks' => 'nullable|string'
        ]);

        try {
            $oldNotes = $invoice->notes;
            
            $invoice->update([
                'status' => $request->status,
                'notes' => $request->remarks ? 
                    ($oldNotes . ' | Status Update: ' . $request->remarks) : 
                    $oldNotes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate Remita payment for admin
     */
    public function initiateAdminRemita(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_setup_id' => 'required|exists:payment_setups,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Get payment setup details
            $paymentSetup = PaymentSetup::findOrFail($request->payment_setup_id);
            $student = Student::with('user')->findOrFail($request->student_id);

            // Generate unique order ID for Remita (to avoid duplicate transaction ID)
            $orderId = 'ADMIN_' . strtoupper(uniqid()) . '_' . time();
            
            // Create invoice with Pending status
            $invoice = Invoice::create([
                'invoice_number' => 'ADMIN-' . strtoupper(uniqid()),
                'student_id' => $request->student_id,
                'academic_session_id' => $request->session_id,
                'term_id' => $request->term_id,
                'total_amount' => $request->amount,
                'amount_paid' => 0,
                'balance' => $request->amount,
                'due_date' => now()->addDays(30),
                'status' => 'Pending',
                'notes' => $request->description ?? 'Admin initiated payment via Remita - ' . $paymentSetup->payment_type,
                'metadata' => json_encode([
                    'payment_method' => 'Remita',
                    'initiated_by' => 'admin',
                    'admin_user_id' => auth()->id(),
                    'service_name' => $paymentSetup->payment_type,
                    'remita_order_id' => $orderId
                ])
            ]);

            // Create invoice item
            DB::table('invoice_items')->insert([
                'invoice_id' => $invoice->id,
                'payment_setup_id' => $paymentSetup->id,
                'amount' => $request->amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Generate RRR via Remita
            $remitaService = new \App\Services\RemitaService();
            $rrr = $remitaService->generateRRR([
                'amount' => $request->amount,
                'payerName' => $student->user->name,
                'payerEmail' => $student->user->email,
                'payerPhone' => $student->user->phone ?? '08000000000',
                'description' => $paymentSetup->payment_type . ' - ' . $student->student_id,
                'orderId' => $orderId
            ]);

            if (!$rrr) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate RRR from Remita'
                ], 500);
            }

            // Update invoice with RRR
            $invoice->update([
                'invoice_number' => $rrr
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'rrr' => $rrr,
                'orderId' => $orderId,
                'amount' => $request->amount,
                'invoiceId' => $invoice->id,
                'publicKey' => config('remita.public_key'),
                'merchantId' => config('remita.merchant_id')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Admin Remita initiation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Remita payment for admin
     */
    public function verifyAdminRemita(Request $request)
    {
        $request->validate([
            'rrr' => 'required|string',
            'invoice_id' => 'required|exists:invoices,id'
        ]);

        try {
            $invoice = Invoice::findOrFail($request->invoice_id);

            // Verify payment with Remita
            $remitaService = new \App\Services\RemitaService();
            $status = $remitaService->verifyPayment($request->rrr);

            if ($status === 'success') {
                DB::beginTransaction();

                // Update invoice
                $invoice->update([
                    'amount_paid' => $invoice->total_amount,
                    'balance' => 0,
                    'status' => 'Paid'
                ]);

                // Create payment record
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                    'amount' => $invoice->total_amount,
                    'payment_date' => now(),
                    'payment_method' => 'Remita',
                    'transaction_id' => $request->rrr,
                    'notes' => 'Admin initiated Remita payment - Verified successfully',
                    'received_by' => auth()->id()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'status' => 'success'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not yet confirmed',
                    'status' => $status
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Admin Remita verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display salary setup page
     */
    public function salarySetup()
    {
        $salaryStructures = SalaryStructure::orderBy('created_at', 'desc')->get();
        
        return view('admin.payments.salary-setup', compact('salaryStructures'));
    }

    /**
     * Store new salary structure
     */
    public function storeSalarySetup(Request $request)
    {
        $request->validate([
            'structure_title' => 'required|string|max:255',
            'role_level' => 'required|string|max:255|unique:salary_structures,role_level',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        SalaryStructure::create([
            'structure_title' => $request->structure_title,
            'role_level' => $request->role_level,
            'base_salary' => $request->base_salary,
            'allowance' => $request->allowance ?? 0,
            'deduction' => $request->deduction ?? 0,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salary structure created successfully'
        ]);
    }

    /**
     * Update salary structure
     */
    public function updateSalarySetup(Request $request, SalaryStructure $salaryStructure)
    {
        $request->validate([
            'structure_title' => 'required|string|max:255',
            'role_level' => 'required|string|max:255|unique:salary_structures,role_level,' . $salaryStructure->id,
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        $salaryStructure->update([
            'structure_title' => $request->structure_title,
            'role_level' => $request->role_level,
            'base_salary' => $request->base_salary,
            'allowance' => $request->allowance ?? 0,
            'deduction' => $request->deduction ?? 0,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salary structure updated successfully'
        ]);
    }

    /**
     * Delete salary structure
     */
    public function deleteSalarySetup(SalaryStructure $salaryStructure)
    {
        try {
            // Check if salary structure is being used in payroll records
            $payrollCount = PayrollRecord::where('staff_id', function($query) use ($salaryStructure) {
                $query->select('staff.id')
                      ->from('staff')
                      ->join('users', 'staff.user_id', '=', 'users.id')
                      ->join('roles', 'users.role_id', '=', 'roles.id')
                      ->where('roles.name', $salaryStructure->role_level);
            })->count();

            if ($payrollCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete salary structure. It is being used in payroll records.'
                ], 400);
            }

            $salaryStructure->delete();

            return response()->json([
                'success' => true,
                'message' => 'Salary structure deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete salary structure: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display reports page
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('report_type', 'all');
        $level = $request->get('level', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $query = Transaction::query();

        // Apply filters
        if ($reportType !== 'all') {
            if ($reportType === 'fees_collected') {
                $query->where('transaction_type', 'Income')->where('status', 'Paid');
            } elseif ($reportType === 'outstanding_fees') {
                $query->where('transaction_type', 'Income')->where('status', 'Pending');
            } elseif ($reportType === 'payroll') {
                $query->where('transaction_type', 'Expense')->where('payment_type', 'Salary');
            }
        }

        if ($level !== 'all') {
            $query->where('level', $level);
        }

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        // Calculate summary
        $totalCollected = (clone $query)->where('transaction_type', 'Income')
            ->where('status', 'Paid')->sum('amount');
        
        $outstandingFees = (clone $query)->where('transaction_type', 'Income')
            ->where('status', 'Pending')->sum('amount');
        
        $payrollExpense = (clone $query)->where('transaction_type', 'Expense')
            ->where('payment_type', 'Salary')->sum('amount');

        // Get report data
        $reportData = $query->selectRaw('payment_type, level, SUM(amount) as amount')
            ->groupBy('payment_type', 'level')
            ->orderBy('payment_type')
            ->get();

        $summary = [
            'total_collected' => $totalCollected,
            'outstanding_fees' => $outstandingFees,
            'payroll_expense' => $payrollExpense
        ];
        
        $levels = SchoolClass::distinct()->pluck('level');

        return view('admin.payments.reports', compact('summary', 'reportData', 'levels'));
    }

    /**
     * Generate Financial Report PDF
     */
    public function generateReportPDF(Request $request)
    {
        $reportType = $request->get('report_type', 'all');
        $level = $request->get('level', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Get the same data as the regular reports method
        $query = Transaction::query();

        if ($reportType !== 'all') {
            if ($reportType === 'fees_collected') {
                $query->where('transaction_type', 'Income')->where('status', 'Paid');
            } elseif ($reportType === 'outstanding_fees') {
                $query->where('transaction_type', 'Income')->where('status', 'Pending');
            } elseif ($reportType === 'payroll') {
                $query->where('transaction_type', 'Expense')->where('payment_type', 'Salary');
            }
        }

        if ($level !== 'all') {
            $query->where('level', $level);
        }

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        // Calculate summary
        $totalCollected = (clone $query)->where('transaction_type', 'Income')
            ->where('status', 'Paid')->sum('amount');
        
        $outstandingFees = (clone $query)->where('transaction_type', 'Income')
            ->where('status', 'Pending')->sum('amount');
        
        $payrollExpense = (clone $query)->where('transaction_type', 'Expense')
            ->where('payment_type', 'Salary')->sum('amount');

        $reportData = $query->selectRaw('payment_type, level, SUM(amount) as amount')
            ->groupBy('payment_type', 'level')
            ->orderBy('payment_type')
            ->get();

        $summary = [
            'total_collected' => $totalCollected,
            'outstanding_fees' => $outstandingFees,
            'payroll_expense' => $payrollExpense
        ];

        // Prepare data for PDF view
        $reportTypeText = $reportType === 'all' ? 'All Categories' : str_replace('_', ' ', ucwords($reportType));
        $levelText = $level === 'all' ? 'All Levels' : $level;
        $dateRangeText = ($dateFrom ?: 'Beginning') . ' to ' . ($dateTo ?: 'Present');

        // Generate PDF using Blade template
        $pdf = Pdf::loadView('admin.payments.pdf.financial-report', compact(
            'summary', 'reportData', 'reportTypeText', 'levelText', 'dateRangeText'
        ));

        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'fontHeightRatio' => 1.0,
                'dpi' => 150,
                'enable_font_subsetting' => true,
                'isJavascriptEnabled' => false,
            ]);

        return $pdf->stream('financial_report_' . date('Y-m-d') . '.pdf', [
            'Attachment' => false,
            'compress' => true,
        ]);
    }

    /**
     * Generate Payroll PDF
     */
    public function generatePayrollPDF(Request $request)
    {
        $month = $request->get('month', now()->format('F'));
        $year = $request->get('year', now()->year);
        $department = $request->get('department');

        // Get staff with their salary structures
        $staffQuery = Staff::with(['user.role'])
            ->where('status', 'Active');

        if ($department) {
            $staffQuery->where('department', $department);
        }

        $staff = $staffQuery->get();

        // Get existing payroll records for the selected month/year
        $existingPayroll = PayrollRecord::where('payroll_month', $month)
            ->where('payroll_year', $year)
            ->with('staff.user')
            ->get()
            ->keyBy('staff_id');

        // Get salary structures
        $salaryStructures = SalaryStructure::where('status', 'Active')->get()->keyBy('role_level');

        // Generate PDF using Blade template
        $pdf = Pdf::loadView('admin.payments.pdf.payroll-report', compact(
            'staff', 'existingPayroll', 'salaryStructures', 'month', 'year', 'department'
        ));

        $pdf->setPaper('A4', 'portrait')->setOptions([
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
        ]);

        return $pdf->stream('payroll_' . $month . '_' . $year . '.pdf');
    }

    /**
     * Generate Receipt PDF
     */
    public function generateReceiptPDF(Transaction $transaction)
    {
        $transaction->load('student.user');
        
        // Generate PDF using Blade template
        $pdf = Pdf::loadView('admin.payments.pdf.payment-receipt', compact('transaction'));

        $pdf->setPaper('A4', 'portrait')->setOptions([
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
        ]);

        return $pdf->stream('receipt_' . $transaction->reference_number . '.pdf');
    }

    /**
     * Show invoice details (Admin)
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load([
            'student.user',
            'student.classArm.schoolClass',
            'academicSession',
            'term',
            'items.paymentSetup',
            'payments'
        ]);
        
        return view('admin.payments.invoice-details', compact('invoice'));
    }

    /**
     * Download invoice receipt as PDF (Admin)
     */
    public function downloadInvoiceReceipt(Invoice $invoice)
    {
        // Load relationships
        $invoice->load([
            'student.user',
            'student.classArm.schoolClass',
            'academicSession',
            'term',
            'items.paymentSetup',
            'payments'
        ]);
        
        // Get payment record
        $payment = $invoice->payments()->latest()->first();
        
        // Get school settings
        $schoolSettings = \App\Models\SchoolSettings::first();
        if (!$schoolSettings) {
            $schoolSettings = \App\Models\SchoolSettings::create([
                'school_name' => 'Secondary School Portal',
                'school_address' => 'School Address',
                'phone_number' => 'N/A',
                'email' => 'info@school.com'
            ]);
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('admin.payments.invoice-receipt', compact('invoice', 'payment', 'schoolSettings'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Stream PDF to browser
        $fileName = 'Receipt_' . $invoice->invoice_number . '_' . date('Y-m-d') . '.pdf';
        return $pdf->stream($fileName);
    }

}
