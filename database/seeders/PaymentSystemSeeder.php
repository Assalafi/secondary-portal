<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentSetup;
use App\Models\SalaryStructure;
use App\Models\Transaction;
use App\Models\PayrollRecord;
use App\Models\Student;
use App\Models\Staff;
use App\Models\SchoolClass;
use Carbon\Carbon;

class PaymentSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available levels from school classes
        $levels = SchoolClass::distinct()->pluck('level')->toArray();
        
        // If no levels exist, use default ones
        if (empty($levels)) {
            $levels = ['Nursery', 'Primary', 'Secondary'];
        }
        
        // Create Payment Setups
        $paymentSetups = [];
        
        // Create school fees setup based on available levels
        $feeAmounts = [
            'Nursery' => 80000.00,
            'Primary' => 120000.00,
            'Secondary' => 180000.00,
            'Pre-Nursery' => 60000.00,
            'JSS' => 150000.00,
            'SSS' => 200000.00
        ];
        
        foreach ($levels as $level) {
            $amount = $feeAmounts[$level] ?? 100000.00; // Default amount if level not in predefined amounts
            $paymentSetups[] = [
                'payment_type' => 'School Fees',
                'level' => $level,
                'term' => 'All',
                'amount' => $amount,
                'effective_date' => now()->startOfYear(),
                'last_updated' => now(),
                'status' => 'Active',
                'description' => $level . ' school fees for all terms'
            ];
        }
        
        // Add other payment types
        $paymentSetups = array_merge($paymentSetups, [
            [
                'payment_type' => 'ID card',
                'level' => 'All',
                'term' => 'All',
                'amount' => 2000.00,
                'effective_date' => now()->startOfYear(),
                'last_updated' => now(),
                'status' => 'Active',
                'description' => 'Student ID card fee'
            ],
            [
                'payment_type' => 'Uniform',
                'level' => 'All',
                'term' => 'All',
                'amount' => 15000.00,
                'effective_date' => now()->startOfYear(),
                'last_updated' => now(),
                'status' => 'Active',
                'description' => 'School uniform package'
            ]
        ]);
        
        // Add Books payment for each level if they exist
        $bookAmounts = [
            'Nursery' => 15000.00,
            'Primary' => 25000.00,
            'Secondary' => 45000.00,
            'Pre-Nursery' => 10000.00,
            'JSS' => 35000.00,
            'SSS' => 50000.00
        ];
        
        foreach ($levels as $level) {
            if (isset($bookAmounts[$level])) {
                $paymentSetups[] = [
                    'payment_type' => 'Books',
                    'level' => $level,
                    'term' => 'All',
                    'amount' => $bookAmounts[$level],
                    'effective_date' => now()->startOfYear(),
                    'last_updated' => now(),
                    'status' => 'Active',
                    'description' => $level . ' textbooks package'
                ];
            }
        }

        foreach ($paymentSetups as $setup) {
            PaymentSetup::create($setup);
        }

        // Create Salary Structures
        $salaryStructures = [
            [
                'structure_title' => 'Principal Package',
                'role_level' => 'Principal',
                'base_salary' => 500000.00,
                'allowance' => 100000.00,
                'deduction' => 50000.00,
                'status' => 'Active',
                'description' => 'Principal compensation package'
            ],
            [
                'structure_title' => 'Vice Principal Package',
                'role_level' => 'Vice Principal',
                'base_salary' => 400000.00,
                'allowance' => 80000.00,
                'deduction' => 40000.00,
                'status' => 'Active',
                'description' => 'Vice Principal compensation package'
            ],
            [
                'structure_title' => 'Head Teacher Package',
                'role_level' => 'Head Teacher',
                'base_salary' => 300000.00,
                'allowance' => 60000.00,
                'deduction' => 30000.00,
                'status' => 'Active',
                'description' => 'Head Teacher compensation package'
            ],
            [
                'structure_title' => 'Senior Teacher Package',
                'role_level' => 'Senior Teacher',
                'base_salary' => 250000.00,
                'allowance' => 50000.00,
                'deduction' => 25000.00,
                'status' => 'Active',
                'description' => 'Senior Teacher compensation package'
            ],
            [
                'structure_title' => 'Class Teacher Package',
                'role_level' => 'Class Teacher',
                'base_salary' => 200000.00,
                'allowance' => 40000.00,
                'deduction' => 20000.00,
                'status' => 'Active',
                'description' => 'Class Teacher compensation package'
            ],
            [
                'structure_title' => 'Subject Teacher Package',
                'role_level' => 'Subject Teacher',
                'base_salary' => 180000.00,
                'allowance' => 30000.00,
                'deduction' => 18000.00,
                'status' => 'Active',
                'description' => 'Subject Teacher compensation package'
            ],
            [
                'structure_title' => 'Admin Staff Package',
                'role_level' => 'Admin Staff',
                'base_salary' => 150000.00,
                'allowance' => 25000.00,
                'deduction' => 15000.00,
                'status' => 'Active',
                'description' => 'Administrative staff compensation package'
            ]
        ];

        foreach ($salaryStructures as $structure) {
            SalaryStructure::create($structure);
        }

        // Create sample transactions (only if students exist)
        if (Student::count() > 0) {
            $students = Student::take(10)->get();
            $paymentTypes = ['School Fees', 'ID card', 'Uniform', 'Books'];
            $transactionLevels = SchoolClass::distinct()->pluck('level')->toArray();
            if (empty($transactionLevels)) {
                $transactionLevels = ['Nursery', 'Primary', 'Secondary'];
            }
            $statuses = ['Paid', 'Pending'];

            foreach ($students as $student) {
                // Create 2-3 transactions per student
                for ($i = 0; $i < rand(2, 3); $i++) {
                    Transaction::create([
                        'student_id' => $student->id,
                        'transaction_type' => 'Income',
                        'payment_type' => $paymentTypes[array_rand($paymentTypes)],
                        'level' => $transactionLevels[array_rand($transactionLevels)],
                        'term' => 'Term ' . rand(1, 3),
                        'amount' => rand(2000, 180000),
                        'status' => $statuses[array_rand($statuses)],
                        'payment_date' => Carbon::now()->subDays(rand(1, 90)),
                        'due_date' => Carbon::now()->addDays(rand(30, 90)),
                        'payment_method' => 'Bank Transfer',
                        'reference_number' => 'TXN' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
                        'description' => 'Student payment for academic session'
                    ]);
                }
            }
        }

        // Create sample payroll records (only if staff exist)
        if (Staff::count() > 0) {
            $staff = Staff::with('user.role')->take(5)->get();
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August'];
            
            foreach ($staff as $staffMember) {
                $roleName = $staffMember->user->role->name ?? 'Class Teacher';
                $salaryStructure = SalaryStructure::where('role_level', $roleName)->first();
                
                if ($salaryStructure) {
                    foreach ($months as $month) {
                        $basePay = $salaryStructure->base_salary;
                        $allowances = $salaryStructure->allowance;
                        $deductions = $salaryStructure->deduction;
                        $grossPay = $basePay + $allowances;
                        $netPay = $grossPay - $deductions;

                        PayrollRecord::create([
                            'staff_id' => $staffMember->id,
                            'payroll_month' => $month,
                            'payroll_year' => now()->year,
                            'base_pay' => $basePay,
                            'allowances' => $allowances,
                            'deductions' => $deductions,
                            'gross_pay' => $grossPay,
                            'net_pay' => $netPay,
                            'status' => rand(0, 1) ? 'Generated' : 'Paid',
                            'generated_date' => Carbon::create(now()->year, array_search($month, $months) + 1, 1),
                            'paid_date' => rand(0, 1) ? Carbon::create(now()->year, array_search($month, $months) + 1, rand(25, 30)) : null,
                            'notes' => 'Monthly salary payment'
                        ]);
                    }
                }
            }
        }

        $this->command->info('Payment system sample data created successfully!');
    }
}
