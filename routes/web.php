<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AcademicManagementController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\TestExamScheduleController;
use App\Http\Controllers\Admin\ScoreUploadController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\GradingSystemController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\ReportSettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\Student\StudentPaymentController;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login/{role}', [AuthController::class, 'showLoginForm'])->name('login.role');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Parent Self-Registration Routes
Route::get('/parent/register', [AuthController::class, 'showParentRegisterForm'])->name('parent.register');
Route::post('/parent/register', [AuthController::class, 'registerParent'])->name('parent.register.store');

// Public Report Verification Route
Route::get('/verify-result/{verificationCode}', [ReportCardController::class, 'verifyResult'])->name('verify-result');

// Student Report Card Routes (outside main student group to avoid conflicts)
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/report-cards', [ReportCardController::class, 'studentReports'])->name('report-cards');
    Route::get('/report-cards/{id}', [ReportCardController::class, 'studentReportShow'])->name('report-cards.show');
});

// Parent Portal Routes
Route::middleware(['auth'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/report-cards', [ReportCardController::class, 'parentReports'])->name('report-cards');
    Route::get('/report-cards/{id}', [ReportCardController::class, 'parentReportShow'])->name('report-cards.show');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Student Management Routes
        Route::prefix('students')->name('students.')->group(function () {
            // Overview and main pages
            Route::get('/', [StudentController::class, 'index'])->name('index');
            Route::get('/overview', [StudentController::class, 'overview'])->name('overview');
            
            // Enrollment multi-step form
            Route::get('/enroll/step1', [StudentController::class, 'enrollStep1'])->name('enroll.step1');
            Route::post('/enroll/step2', [StudentController::class, 'enrollStep2'])->name('enroll.step2');
            Route::get('/enroll/step2', [StudentController::class, 'showEnrollStep2'])->name('enroll.step2.show');
            Route::post('/enroll/step3', [StudentController::class, 'enrollStep3'])->name('enroll.step3');
            Route::get('/enroll/step3', [StudentController::class, 'showEnrollStep3'])->name('enroll.step3.show');
            Route::post('/enroll/step4', [StudentController::class, 'enrollStep4'])->name('enroll.step4');
            Route::get('/enroll/step4', [StudentController::class, 'showEnrollStep4'])->name('enroll.step4.show');
            Route::post('/enroll/complete', [StudentController::class, 'completeEnrollment'])->name('enroll.complete');
            
            // Student profile tabs
            Route::get('/{student}/profile/overview', [StudentController::class, 'profileOverview'])->name('profile.overview');
            Route::get('/{student}/profile/academic', [StudentController::class, 'profileAcademic'])->name('profile.academic');
            Route::get('/{student}/profile/academic/pdf', [StudentController::class, 'profileAcademicPdf'])->name('profile.academic.pdf');
            Route::get('/{student}/profile/fees', [StudentController::class, 'profileFees'])->name('profile.fees');
            Route::get('/{student}/profile/attendance', [StudentController::class, 'profileAttendance'])->name('profile.attendance');
            Route::get('/{student}/profile/documents', [StudentController::class, 'profileDocuments'])->name('profile.documents');
            Route::get('/{student}/profile/pdf', [StudentController::class, 'profilePdf'])->name('profile.pdf');
            
            // Promote/Transfer
            Route::get('/promote', [StudentController::class, 'promoteIndex'])->name('promote.index');
            Route::get('/promote/{class}', [StudentController::class, 'promoteClass'])->name('promote.class');
            Route::post('/promote/execute', [StudentController::class, 'executePromotion'])->name('promote.execute');
            Route::post('/transfer/execute', [StudentController::class, 'executeTransfer'])->name('transfer.execute');
            
            // CRUD operations
            Route::get('/create', [StudentController::class, 'create'])->name('create');
            Route::post('/', [StudentController::class, 'store'])->name('store');
            Route::get('/{student}', [StudentController::class, 'show'])->name('show');
            Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
            Route::put('/{student}', [StudentController::class, 'update'])->name('update');
            Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
            
            // Additional actions
            Route::post('/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('reset-password');
            Route::post('/bulk-action', [StudentController::class, 'bulkAction'])->name('bulk-action');
        });
        
        // Utility routes
        Route::get('/get-class-arms', [StudentController::class, 'getClassArms'])->name('get-class-arms');
    });

    // Admin Routes - Payment & Finance
    Route::prefix('admin/payments')->name('admin.payments.')->middleware(['auth'])->group(function () {
        Route::get('/', [PaymentController::class, 'overview'])->name('overview');
        Route::get('/payroll', [PaymentController::class, 'payroll'])->name('payroll');
        Route::post('/generate-payroll', [PaymentController::class, 'generatePayroll'])->name('generate-payroll');
        Route::get('/setup', [PaymentController::class, 'paymentSetup'])->name('setup');
        Route::post('/setup', [PaymentController::class, 'storePaymentSetup'])->name('setup.store');
        Route::put('/setup/{paymentSetup}', [PaymentController::class, 'updatePaymentSetup'])->name('setup.update');
        Route::delete('/setup/{paymentSetup}', [PaymentController::class, 'deletePaymentSetup'])->name('setup.delete');
        Route::get('/fees-income', [PaymentController::class, 'feesIncome'])->name('fees-income');
        Route::post('/fees-income/record', [PaymentController::class, 'recordPayment'])->name('fees-income.record');
        Route::put('/fees-income/{invoice}', [PaymentController::class, 'updateTransactionStatus'])->name('fees-income.update');
        Route::post('/remita/initiate', [PaymentController::class, 'initiateAdminRemita'])->name('remita.initiate');
        Route::post('/remita/verify', [PaymentController::class, 'verifyAdminRemita'])->name('remita.verify');
        Route::get('/salary-setup', [PaymentController::class, 'salarySetup'])->name('salary-setup');
        Route::post('/salary-setup', [PaymentController::class, 'storeSalarySetup'])->name('salary-setup.store');
        Route::put('/salary-setup/{salaryStructure}', [PaymentController::class, 'updateSalarySetup'])->name('salary-setup.update');
        Route::delete('/salary-setup/{salaryStructure}', [PaymentController::class, 'deleteSalarySetup'])->name('salary-setup.delete');
        Route::get('/reports', [PaymentController::class, 'reports'])->name('reports');
        Route::get('/reports/pdf', [PaymentController::class, 'generateReportPDF'])->name('reports.pdf');
        Route::get('/payroll/pdf', [PaymentController::class, 'generatePayrollPDF'])->name('payroll.pdf');
        Route::get('/receipt/{transaction}/pdf', [PaymentController::class, 'generateReceiptPDF'])->name('receipt.pdf');
        Route::get('/invoice/{invoice}', [PaymentController::class, 'showInvoice'])->name('invoice.show');
        Route::get('/invoice/{invoice}/receipt', [PaymentController::class, 'downloadInvoiceReceipt'])->name('invoice.receipt');
    });
    
    // API Routes for AJAX calls
    Route::get('/api/students/all', [PaymentController::class, 'getStudents'])->middleware(['auth'])->name('api.students.all');
    
    // Location API routes
    Route::get('/api/locations/states', [LocationController::class, 'getStates'])->name('api.locations.states');
    Route::get('/api/locations/lgas', [LocationController::class, 'getLgas'])->name('api.locations.lgas');

    // Admin Routes - Staff Management
    Route::prefix('admin/staff')->name('admin.staff.')->middleware(['auth'])->group(function () {
        // Staff Overview (default page)
        Route::get('/', [StaffController::class, 'overview'])->name('overview');
        
        // Staff Directory
        Route::get('/directory', [StaffController::class, 'index'])->name('index');
        
        // Staff Assignments
        Route::get('/assignments', [StaffController::class, 'assignments'])->name('assignments');
        Route::get('/get-class-arms/{classId}', [StaffController::class, 'getClassArms'])->name('get-class-arms');
        Route::post('/assign-role-class', [StaffController::class, 'assignRoleClass'])->name('assign-role-class');
        
        // Staff Enrollment (Multi-step)
        Route::prefix('enroll')->name('enroll.')->group(function () {
            Route::get('/step1', [StaffController::class, 'enrollStep1'])->name('step1');
            Route::post('/step1', [StaffController::class, 'enrollStep1Store'])->name('step1.store');
            Route::get('/step2', [StaffController::class, 'enrollStep2'])->name('step2');
            Route::post('/step2', [StaffController::class, 'enrollStep2Store'])->name('step2.store');
            Route::get('/step3', [StaffController::class, 'enrollStep3'])->name('step3');
            Route::post('/step3', [StaffController::class, 'enrollStep3Store'])->name('step3.store');
            Route::get('/step4', [StaffController::class, 'enrollStep4'])->name('step4');
            Route::post('/complete', [StaffController::class, 'completeEnrollment'])->name('complete');
        });
        
        // Staff Profile Routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/{staff}/overview', [StaffController::class, 'show'])->name('overview');
        });
        
        // CRUD operations
        Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
        
        // Additional actions
        Route::post('/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('reset-password');
        Route::post('/bulk-action', [StaffController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Admin Routes - Parent/Guardian Management
    Route::prefix('admin/parent-guardians')->name('admin.parent-guardians.')->middleware(['auth'])->group(function () {
        // Overview page
        Route::get('/', [\App\Http\Controllers\ParentGuardianController::class, 'overview'])->name('overview');
        
        // Directory/List
        Route::get('/directory', [\App\Http\Controllers\ParentGuardianController::class, 'index'])->name('index');
        
        // Create new parent/guardian
        Route::get('/create', [\App\Http\Controllers\ParentGuardianController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ParentGuardianController::class, 'store'])->name('store');
        
        // View, Edit, Delete specific parent/guardian
        Route::get('/{parentGuardian}', [\App\Http\Controllers\ParentGuardianController::class, 'show'])->name('show');
        Route::get('/{parentGuardian}/edit', [\App\Http\Controllers\ParentGuardianController::class, 'edit'])->name('edit');
        Route::put('/{parentGuardian}', [\App\Http\Controllers\ParentGuardianController::class, 'update'])->name('update');
        Route::delete('/{parentGuardian}', [\App\Http\Controllers\ParentGuardianController::class, 'destroy'])->name('destroy');
        
        // Additional actions
        Route::post('/{parentGuardian}/reset-password', [\App\Http\Controllers\ParentGuardianController::class, 'resetPassword'])->name('reset-password');
        Route::post('/bulk-action', [\App\Http\Controllers\ParentGuardianController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Admin Routes - Admission Applications
    Route::prefix('admin/admissions')->name('admin.admissions.')->middleware(['auth'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'show'])->name('show');
        Route::get('/{application}/review', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'review'])->name('review');
        Route::post('/{application}/approve', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'reject'])->name('reject');
        Route::delete('/{application}', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'destroy'])->name('destroy');
    });
    
    // Admin Routes - Classes & Subjects
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        
        // Classes & Subjects Overview
        Route::get('/classes-subjects', [ClassSubjectController::class, 'overview'])->name('classes-subjects.overview');
        
        // Classes management
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [ClassSubjectController::class, 'classesIndex'])->name('index');
            Route::get('/create', [ClassSubjectController::class, 'classesCreate'])->name('create');
            Route::post('/', [ClassSubjectController::class, 'classesStore'])->name('store');
            Route::get('get-class-details', [ClassSubjectController::class, 'getClassDetailsByLevel'])->name('getDetails');
            Route::get('/{class}/{arm?}', [ClassSubjectController::class, 'classesShow'])->name('show');
            Route::get('/{class}/edit', [ClassSubjectController::class, 'classesEdit'])->name('edit');
            Route::put('/{class}', [ClassSubjectController::class, 'classesUpdate'])->name('update');
            Route::delete('/{class}', [ClassSubjectController::class, 'classesDestroy'])->name('destroy');
            Route::delete('/{class}/students/{student}/remove', [ClassSubjectController::class, 'removeStudentFromClass'])->name('students.remove');
        });
        
        // Subjects management
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [ClassSubjectController::class, 'subjectsIndex'])->name('index');
            Route::get('/create', [ClassSubjectController::class, 'subjectsCreate'])->name('create');
            Route::post('/', [ClassSubjectController::class, 'subjectsStore'])->name('store');
            Route::get('/{subject}', [ClassSubjectController::class, 'subjectsShow'])->name('show');
            Route::get('/{subject}/edit', [ClassSubjectController::class, 'subjectsEdit'])->name('edit');
            Route::put('/{subject}', [ClassSubjectController::class, 'subjectsUpdate'])->name('update');
            Route::delete('/{subject}', [ClassSubjectController::class, 'subjectsDestroy'])->name('destroy');
        });

        // Utility endpoint for dynamic dropdowns on Subjects modal
        Route::get('/get-class-details-by-level', [ClassSubjectController::class, 'getClassDetailsByLevel'])->name('get-class-details-by-level');
        
        // Teacher Assignment
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/assign', [ClassSubjectController::class, 'teacherAssign'])->name('assign');
            Route::post('/assign', [ClassSubjectController::class, 'teacherAssignStore'])->name('assign.store');
            Route::delete('/assign/{class_arm}', [ClassSubjectController::class, 'removeTeacher'])->name('assign.remove');
        });
        
        Route::delete('/class-arms/{class_arm}/subjects/{subject}/remove', [ClassSubjectController::class, 'removeSubjectFromClass'])->name('class-arms.subjects.remove');
        Route::post('/class-arms/{class_arm}/subjects/add', [ClassSubjectController::class, 'addSubjectToClass'])->name('class-arms.subjects.add');

        // Class Arms management
        Route::prefix('class-arms')->name('class-arms.')->group(function () {
            Route::get('/', [ClassSubjectController::class, 'classArmsIndex'])->name('index');
            Route::get('/create', [ClassSubjectController::class, 'classArmsCreate'])->name('create');
            Route::post('/', [ClassSubjectController::class, 'classArmsStore'])->name('store');
            Route::put('/{class_arm}/update-teacher', [ClassSubjectController::class, 'updateTeacher'])->name('update-teacher');
        });

        // Academic Management Routes
        Route::prefix('academic-management')->name('academic-management.')->group(function () {
            Route::get('/', [AcademicManagementController::class, 'index'])->name('index');
            
            // Attendance Routes
            Route::prefix('attendance')->name('attendance.')->group(function () {
                Route::get('/', [AttendanceController::class, 'index'])->name('index');
                Route::get('/{classId}', [AttendanceController::class, 'take'])->name('take');
                Route::post('/', [AttendanceController::class, 'store'])->name('store');
                Route::get('/{classId}/history', [AttendanceController::class, 'history'])->name('history');
            });
            
            // Assignment Routes
            Route::prefix('assignments')->name('assignments.')->group(function () {
                Route::get('/', [AssignmentController::class, 'index'])->name('index');
                Route::get('/create', [AssignmentController::class, 'create'])->name('create');
                Route::post('/', [AssignmentController::class, 'store'])->name('store');
                Route::get('/{assignmentId}', [AssignmentController::class, 'show'])->name('show');
                Route::get('/{assignmentId}/edit', [AssignmentController::class, 'edit'])->name('edit');
                Route::put('/{assignmentId}', [AssignmentController::class, 'update'])->name('update');
                Route::delete('/{assignmentId}', [AssignmentController::class, 'destroy'])->name('destroy');
            });
            
            // Test/Exam Schedule Routes
            Route::prefix('test-exam-schedule')->name('test-exam-schedule.')->group(function () {
                Route::get('/', [TestExamScheduleController::class, 'index'])->name('index');
                Route::get('/{classId}', [TestExamScheduleController::class, 'classSchedule'])->name('class');
                Route::get('/{classId}/create', [TestExamScheduleController::class, 'create'])->name('create');
                Route::post('/{classId}', [TestExamScheduleController::class, 'store'])->name('store');
            });
            
            // Score Upload Routes
            Route::prefix('score-upload')->name('score-upload.')->group(function () {
                Route::get('/', [ScoreUploadController::class, 'index'])->name('index');
                Route::get('/{classId}', [ScoreUploadController::class, 'classSubject'])->name('class');
                Route::get('/{classId}/{subjectId}', [ScoreUploadController::class, 'subject'])->name('subject');
                Route::post('/', [ScoreUploadController::class, 'store'])->name('store');
            });
            
            // Result & Grades Routes
            Route::prefix('results')->name('results.')->group(function () {
                Route::get('/', [ResultController::class, 'index'])->name('index');
                Route::get('/{classId}', [ResultController::class, 'classResults'])->name('class');
                Route::get('/{classId}/{studentId}', [ResultController::class, 'studentResult'])->name('student');
                Route::post('/{classId}/{studentId}/generate-termly', [ResultController::class, 'generateTermlyReportCard'])->name('generate-termly-card');
                Route::post('/{classId}/{studentId}/generate-annual', [ResultController::class, 'generateAnnualReportCard'])->name('generate-annual-card');
                Route::get('/{classId}/{studentId}/view-report', [ResultController::class, 'viewReportCard'])->name('view-report-card');
            });
            
            // Report Card Routes
            Route::prefix('report-cards')->name('report-cards.')->group(function () {
                Route::get('/{id}', [ReportCardController::class, 'show'])->name('show');
                Route::post('/{id}/comments', [ReportCardController::class, 'updateComments'])->name('update-comments');
                Route::get('/{id}/comments/edit', [ReportCardController::class, 'editComments'])->name('edit-comments');
                Route::post('/{id}/domain-ratings', [ReportCardController::class, 'updateDomainRatings'])->name('update-domain-ratings');
                Route::get('/{id}/domain-ratings/edit', [ReportCardController::class, 'editDomainRatings'])->name('edit-domain-ratings');
                Route::post('/{id}/approve', [ReportCardController::class, 'approve'])->name('approve');
                Route::post('/{id}/publish', [ReportCardController::class, 'publish'])->name('publish');
                Route::get('/class/{classId}/broadsheet', [ReportCardController::class, 'broadsheet'])->name('broadsheet');
                Route::post('/class/{classId}/student/{studentId}/termly', [ReportCardController::class, 'generateTermlyReport'])->name('generate-termly');
                Route::post('/class/{classId}/student/{studentId}/annual', [ReportCardController::class, 'generateAnnualReport'])->name('generate-annual');
                Route::get('/{id}/promotion/edit', [ReportCardController::class, 'editPromotion'])->name('edit-promotion');
                Route::post('/{id}/promotion', [ReportCardController::class, 'updatePromotion'])->name('update-promotion');
                Route::post('/{id}/promotion/auto-calculate', [ReportCardController::class, 'autoCalculatePromotion'])->name('auto-calculate-promotion');
                Route::get('/{id}/attendance/edit', [ReportCardController::class, 'editAttendance'])->name('edit-attendance');
                Route::post('/{id}/attendance', [ReportCardController::class, 'updateAttendance'])->name('update-attendance');
                Route::get('/{id}/pdf/generate', [ReportCardController::class, 'generatePDF'])->name('generate-pdf');
                Route::get('/{id}/pdf/download', [ReportCardController::class, 'downloadPDF'])->name('download-pdf');
                Route::get('/{id}/qr/generate', [ReportCardController::class, 'generateQRCode'])->name('generate-qr');
            });
        });

        // 
        // Settings Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            
            // School Information
            Route::get('/school-info', [SettingsController::class, 'schoolInfo'])->name('school-info');
            Route::post('/school-info', [SettingsController::class, 'updateSchoolInfo'])->name('school-info.update');
            
            // Grading System
            Route::get('/grading-system', [GradingSystemController::class, 'index'])->name('grading-system');
            Route::post('/grading-system', [GradingSystemController::class, 'store'])->name('grading-system.store');
            Route::put('/grading-system/{id}', [GradingSystemController::class, 'update'])->name('grading-system.update');
            Route::delete('/grading-system/{id}', [GradingSystemController::class, 'destroy'])->name('grading-system.destroy');
            
            // Report Settings
            Route::get('/report-settings', [ReportSettingsController::class, 'index'])->name('report-settings');
            Route::post('/report-settings', [ReportSettingsController::class, 'update'])->name('report-settings.update');
            
            // Session/Term Management
            Route::get('/session-term', [SettingsController::class, 'sessionTerm'])->name('session-term');
            Route::post('/session-term', [SettingsController::class, 'storeSessionTerm'])->name('session-term.store');
            Route::post('/session-term/{session}/set-current', [SettingsController::class, 'setCurrentSession'])->name('session-term.set-current');
            
            // Security Settings
            Route::get('/security', [SettingsController::class, 'security'])->name('security');
            Route::post('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
            
            // User Management
            Route::get('/user-management', [SettingsController::class, 'userManagement'])->name('user-management');
            Route::post('/user-management', [SettingsController::class, 'createUser'])->name('user-management.store');
            
            // Role & Permissions
            Route::get('/role-permissions', [SettingsController::class, 'rolePermissions'])->name('role-permissions');
            Route::post('/role-permissions', [SettingsController::class, 'createRole'])->name('role-permissions.store');
            
            // Notification Preferences
            Route::get('/notification-preferences', [SettingsController::class, 'notificationPreferences'])->name('notification-preferences');
            Route::post('/notification-preferences', [SettingsController::class, 'updateNotificationPreferences'])->name('notification-preferences.update');
            
            // System Configuration
            Route::get('/system-config', [SettingsController::class, 'systemConfig'])->name('system-config');
            Route::post('/system-config', [SettingsController::class, 'updateSystemConfig'])->name('system-config.update');
            
            // Backup & Restore
            Route::get('/backup-restore', [SettingsController::class, 'backupRestore'])->name('backup-restore');
        });
    });
    
    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('teacher.dashboard');
        })->name('dashboard');
    });
    
    // Accountant Routes
    Route::prefix('accountant')->name('accountant.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('accountant.dashboard');
        })->name('dashboard');
    });
    
    // Librarian Routes
    Route::prefix('librarian')->name('librarian.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('librarian.dashboard');
        })->name('dashboard');
    });
    
    // Student Routes
    Route::prefix('student')->name('student.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Results
        Route::prefix('results')->name('results.')->group(function () {
            Route::get('/', [StudentPortalController::class, 'results'])->name('index');
        });

        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [StudentPortalController::class, 'attendance'])->name('index');
        });

        // Timetable
        Route::get('/timetable', [StudentPortalController::class, 'timetable'])->name('timetable');

        // Profile
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
        Route::post('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password', [StudentPortalController::class, 'updatePassword'])->name('password.update');

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [StudentPaymentController::class, 'index'])->name('index');
            Route::get('/{id}', [StudentPaymentController::class, 'show'])->name('show');
            Route::get('/{id}/receipt', [StudentPaymentController::class, 'downloadReceipt'])->name('receipt');
            Route::post('/remita/initiate', [StudentPaymentController::class, 'initiateRemita'])->name('remita.initiate');
            Route::post('/remita/verify', [StudentPaymentController::class, 'verifyRemita'])->name('remita.verify');
        });
    });
    
    // Parent Routes
    Route::prefix('parent')->name('parent.')->middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Parent\ParentDashboardController::class, 'index'])->name('dashboard');
        
        // My Dependents
        Route::prefix('dependents')->name('dependents.')->group(function () {
            Route::get('/', [App\Http\Controllers\Parent\DependentController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Parent\DependentController::class, 'show'])->name('show');
            Route::get('/{id}/profile', [App\Http\Controllers\Parent\DependentController::class, 'profile'])->name('profile');
            Route::post('/{id}/profile/personal', [App\Http\Controllers\Parent\DependentController::class, 'updatePersonal'])->name('profile.personal.update');
            Route::post('/{id}/profile/parent', [App\Http\Controllers\Parent\DependentController::class, 'updateParent'])->name('profile.parent.update');
            Route::get('/{id}/attendance', [App\Http\Controllers\Parent\DependentController::class, 'attendance'])->name('attendance');
            Route::get('/{id}/assignments', [App\Http\Controllers\Parent\DependentController::class, 'assignments'])->name('assignments');
            Route::get('/{id}/schedule', [App\Http\Controllers\Parent\DependentController::class, 'schedule'])->name('schedule');
            Route::get('/{id}/results', [App\Http\Controllers\Parent\DependentController::class, 'results'])->name('results');
            Route::get('/{id}/payments', [App\Http\Controllers\Parent\DependentController::class, 'payments'])->name('payments');
            Route::post('/{id}/remove', [App\Http\Controllers\Parent\DependentController::class, 'remove'])->name('remove');
        });
        
        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [App\Http\Controllers\Parent\PaymentController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Parent\PaymentController::class, 'show'])->name('show');
            Route::get('/{id}/receipt', [App\Http\Controllers\Parent\PaymentController::class, 'downloadReceipt'])->name('download-receipt');
            Route::post('/calculate', [App\Http\Controllers\Parent\PaymentController::class, 'calculateSchoolFeesAmount'])->name('calculate');
            Route::post('/remita/initiate', [App\Http\Controllers\Parent\PaymentController::class, 'initiateRemita'])->name('remita.initiate');
            Route::post('/remita/verify', [App\Http\Controllers\Parent\PaymentController::class, 'verifyRemita'])->name('remita.verify');
            Route::post('/verify-status', [App\Http\Controllers\Parent\PaymentController::class, 'verifyStatus'])->name('verify-status');
        });
        
        // Support Tickets
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [App\Http\Controllers\Parent\SupportController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Parent\SupportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Parent\SupportController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Parent\SupportController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [App\Http\Controllers\Parent\SupportController::class, 'reply'])->name('reply');
        });
        
        // Admission Applications
        Route::prefix('admission')->name('admission.')->group(function () {
            Route::get('/', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'create'])->name('create');
            Route::post('/initiate-payment', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'initiatePayment'])->name('initiate-payment');
            Route::get('/{application}/payment', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'showPayment'])->name('payment');
            Route::get('/{application}/form', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'showForm'])->name('form');
            Route::put('/{application}/save', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'saveForm'])->name('save');
            Route::post('/{application}/submit', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'submit'])->name('submit');
            Route::get('/{application}/download-pdf', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/{application}', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'show'])->name('show');
            Route::delete('/{application}', [App\Http\Controllers\Parent\AdmissionApplicationController::class, 'destroy'])->name('destroy');
        });
        
        // Account Settings
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [App\Http\Controllers\Parent\AccountController::class, 'index'])->name('index');
            Route::post('/profile', [App\Http\Controllers\Parent\AccountController::class, 'updateProfile'])->name('profile.update');
            Route::post('/password', [App\Http\Controllers\Parent\AccountController::class, 'updatePassword'])->name('password.update');
            Route::post('/notifications', [App\Http\Controllers\Parent\AccountController::class, 'updateNotifications'])->name('notifications.update');
        });
    });
    
    // General Dashboard Route
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});
