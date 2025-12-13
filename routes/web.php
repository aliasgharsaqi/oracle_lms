<?php

use App\Http\Controllers\Admin\AttendenceController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeePaymentController;
use App\Http\Controllers\Admin\FeeReportController;
use App\Http\Controllers\Admin\MarksController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ResultCardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentFeePlanController;
use App\Http\Controllers\Admin\StudentLeaveController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherDiaryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'create'])->middleware('guest');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

// Apply both 'auth' and the new 'is_admin' middleware
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::match(['post', 'get'], 'logout', [LoginController::class, 'destroy'])->name('logout');

    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Teacher Dashboard
    Route::get('/teacher/dashboard', [DashboardController::class, 'teacherDashboard'])->name('user.dashboard');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    // Class Management Routes
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::get('/classes/edit/{id}', [ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/update/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::delete('/classes/delete/{id}', [ClassController::class, 'destroy'])->name('classes.destroy');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/image', [ProfileController::class, 'updateProfileImage'])->name('profile.image.update');

    // Student Routes
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::patch('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Subject Routes
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::patch('/subjects/{subject}/toggle-status', [SubjectController::class, 'toggleStatus'])->name('subjects.toggleStatus');

    // Schedule Management Routes
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{id}', [ScheduleController::class, 'show'])->name('schedules.show');
    Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/admin/get-subjects-by-class/{class_id}', [ScheduleController::class, 'getSubjectsByClass'])->name('admin.getSubjectsByClass');

    // Teacher Routes
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // --- Teacher Diary Module Routes (FULLY DYNAMIC) ---
    // ALIAS for backward compatibility, redirects 'teacher_diary' to 'teacher_diary.index'
    Route::get('/teacher_diary', function () {
        return redirect()->route('teacher_diary.index');
    })->name('teacher_diary');

    Route::controller(TeacherDiaryController::class)->prefix('teacher-diary')->name('teacher_diary.')->group(function () {
        Route::get('/', 'index')->name('index');                           // Main Diary view
        Route::get('/record/{teacher}', 'getTeacherRecord')->name('record'); // AJAX to fetch teacher details
        Route::post('/task', 'storeTask')->name('store_task');             // Modal POST to save assignments (multiple classes/subjects)
        Route::post('/progress/{assignment}', 'updateProgress')->name('update_progress'); // AJAX to update status/notes
        Route::match(['get', 'post'], '/monthly-report', 'monthlyReport')->name('monthly_report'); // Monthly Report View/Filter
        
        // ** FIX: Missing route for dynamic subject loading **
        Route::get('/get-subjects', 'getSubjectsByClasses')->name('get_subjects'); // <-- ADDED
    });

    // Fee Plan & Payment Routes
    Route::get('/fees/plans', [StudentFeePlanController::class, 'index'])->name('fees.plans.index');
    Route::get('/fees/plans/{student}/create', [StudentFeePlanController::class, 'create'])->name('fees.plans.create');
    Route::post('/fees/plans/{student}', [StudentFeePlanController::class, 'store'])->name('fees.plans.store');

    Route::get('/fees/payments', [FeePaymentController::class, 'index'])->name('fees.payments.index');
    Route::post('/fees/payments', [FeePaymentController::class, 'storePayment'])->name('fees.payments.store');
    Route::get('/fees/receipt/{voucher}', [FeePaymentController::class, 'showReceipt'])->name('fees.receipt');
    Route::get('/fees/student-ledger/{student}/{year}', [FeePaymentController::class, 'getStudentLedger'])->name('fees.student.ledger');
    Route::post('fees/generate-voucher/{student}', [FeePaymentController::class, 'generateAndGetVoucher'])->name('fees.generateVoucher');

    // Fee Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('revenue-dashboard', [FeeReportController::class, 'revenueDashboard'])->name('revenue_dashboard');
        Route::get('pending-fees', [FeeReportController::class, 'pendingFees'])->name('pending_fees');
        Route::get('paid-fees', [FeeReportController::class, 'paidFees'])->name('paid_fees');
    });

    // Role & School Routes (Admin Prefix)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('schools', SchoolController::class);
    });

    // Marks Management Routes
    Route::get('marks', [MarksController::class, 'index'])->name('marks.index');
    Route::post('marks', [MarksController::class, 'store'])->name('marks.store');
    Route::get('marks/get-subjects/{class_id}', [MarksController::class, 'getSubjects'])->name('marks.getSubjects');

    // Result Card Routes
    Route::get('/result-cards', [ResultCardController::class, 'index'])->name('result-cards.index');
    Route::get('/result-cards/students/{class_id}', [ResultCardController::class, 'getStudentsByClass'])->name('result-cards.getStudents');
    Route::get('/result-cards/show/{student}/{semester}', [ResultCardController::class, 'showResultCard'])->name('result-cards.show');
    Route::get('/result-cards/pdf/{student_id}/{semester_id}', [ResultCardController::class, 'generatePdf'])->name('result-cards.pdf');

    // Student Attendance Marking & Report Routes
    Route::get('attendance', [StudentAttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance', [StudentAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance-report', [StudentAttendanceController::class, 'report'])->name('attendance.report');
    Route::post('attendance-report', [StudentAttendanceController::class, 'showReport'])->name('attendance.showReport');

    // PDF Generation/Download Routes
    Route::get('/students/{student}/semester/{semester}/result-card/generate', [PdfController::class, 'generateResultCard'])
        ->name('students.result-card.generate');
    Route::get('/students/{student}/semester/{semester}/result-card/download', [PdfController::class, 'downloadResultCard'])
        ->name('students.result-card.download');

    // Teacher Attendance Routes
    Route::get('attendence/teacher', [AttendenceController::class, 'teacher_attendence'])->name('attendence.teacher');
    Route::post('attendence/teacher/mark', [AttendenceController::class, 'mark_attendance'])->name('attendence.teacher.mark');
    Route::post('attendence/teacher/leave', [AttendenceController::class, 'apply_leave'])->name('attendence.teacher.leave');
    Route::post('attendence/teacher/update-past', [AttendenceController::class, 'update_past_attendance'])->name('attendence.teacher.update_past');
    Route::get('attendence/monthly-report', [AttendenceController::class, 'monthly_report'])->name('attendence.teacher.monthly_report');
    Route::post('/attendance/save-time-settings', [AttendenceController::class, 'save_time_settings'])->name('attendence.save_settings');
});
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
 Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
    
    // API Endpoints for CRUD:
    Route::post('/transactions', [TransactionController::class, 'store']); // Create
    // The {transaction} wildcard MUST match the injected model name in the controller method signature
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update']); // Update
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']); // Delete
});
// Teacher Leave Approval Routes (Accessible via 'auth' middleware)
Route::controller(AttendenceController::class)->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('attendance/pending-leaves', 'show_pending_leaves')
        ->name('attendence.teacher.pending_leaves');
    Route::post('attendance/action-on-leave', 'action_on_leave')
        ->name('attendence.teacher.action_on_leave');
});

// Student Leave Submission (Authenticated Students)
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('leaves', [StudentLeaveController::class, 'index'])->name('leaves.index');
    Route::post('leaves', [StudentLeaveController::class, 'store'])->name('leaves.store');
});

// Student Leave Management (Admin/Teacher Access)
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('student-leaves/store', [StudentLeaveController::class, 'adminStore'])->name('student.leaves.store.admin');
    Route::get('student-leaves', [StudentLeaveController::class, 'adminIndex'])->name('student.leaves.index');
    Route::post('student-leaves/action', [StudentLeaveController::class, 'actionOnLeave'])->name('student.leaves.action');
});

Route::prefix('leaves/student')->name('admin.student.leaves.')->group(function () {
    Route::get('/pending', [StudentLeaveController::class, 'adminIndex'])->name('pending');
    Route::post('/store', [StudentLeaveController::class, 'adminStore'])->name('store');
    Route::post('/action', [StudentLeaveController::class, 'actionOnLeave'])->name('action');
});

// Marks Export Route
Route::get('marks/export', [MarksController::class, 'export'])->name('marks.export');

// --- Static Routes ---
Route::get('/student_diary', function () {
    return view('admin.diary.student_diary');
})->name('student_diary');

Route::get('/quiz', function () {
    return view('admin.quiz');
})->name('quiz');

Route::get('/chatbot', function () {
    return view('admin.chatbot');
})->name('chatbot');

// Route::get('/transaction', function () {
//     return view('admin.transaction');
// })->name('transaction');

Route::get('/quize_detail', function () {
    return view('admin.quize_detail');
})->name('quize_detail');