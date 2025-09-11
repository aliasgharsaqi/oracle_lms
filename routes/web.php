<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\FeePaymentController;
use App\Http\Controllers\Admin\FeeStructureController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentFeePlanController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\FeeReportController;
use App\Http\Controllers\Admin\RoleController;




Route::get('/', [LoginController::class, 'create'])->middleware('guest');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

// Apply both 'auth' and the new 'is_admin' middleware
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

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

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::patch('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

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


    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');


    Route::get('/fees/plans', [StudentFeePlanController::class, 'index'])->name('fees.plans.index');
    Route::get('/fees/plans/{student}/create', [StudentFeePlanController::class, 'create'])->name('fees.plans.create');
    Route::post('/fees/plans/{student}', [StudentFeePlanController::class, 'store'])->name('fees.plans.store');

    Route::get('/fees/payments', [FeePaymentController::class, 'index'])->name('fees.payments.index');
    Route::post('/fees/payments', [FeePaymentController::class, 'storePayment'])->name('fees.payments.store');
    Route::get('/fees/receipt/{voucher}', [FeePaymentController::class, 'showReceipt'])->name('fees.receipt');

    Route::get('/pending-fees', [FeeReportController::class, 'pendingFees'])->name('reports.pendingFees');
    Route::get('/paid-fees', [FeeReportController::class, 'paidFees'])->name('reports.paidFees');
    Route::get('/monthly-revenue', [FeeReportController::class, 'monthlyRevenue'])->name('reports.monthlyRevenue');
    Route::get('/total-revenue', [FeeReportController::class, 'totalRevenue'])->name('reports.totalRevenue');
    Route::get('/revenue-dashboard', [FeeReportController::class, 'revenueDashboard'])
        ->name('reports.revenueDashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        // routes/web.php
        Route::resource('roles', RoleController::class)->except(['show']);
    });
});

// web.php
Route::get('/marks', function () {
    return view('admin.marks.index');
})->name('marks.index'); // ✅ name updated

Route::get('/teacher_diary', function () {
    return view('admin.teacher_diary');
})->name('teacher_diary'); // ✅ name updated
