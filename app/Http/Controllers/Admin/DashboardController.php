<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\School;
use App\Models\StudentFeeVoucher;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on the user's role.
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->hasRole(['Super Admin', 'School Admin'])) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('Teacher')) {
            return $this->teacherDashboard();
        }

        // Default dashboard for other roles like 'Staff'
        return $this->staffDashboard();
    }

    /**
     * Data and view for Super Admin and School Admin.
     */
    private function adminDashboard(): View
    {
        $user = Auth::user();
        $financialQuery = StudentFeeVoucher::query();
        
        // Super Admin sees all data, School Admin sees their school's data only
        if ($user->hasRole('Super Admin')) {
            $studentCount = Student::count();
            $teacherCount = Teacher::count();
            $classCount = SchoolClass::count();
            $schoolCount = School::count();
            $schoolName = "Platform Wide";
        } else {
            $school_id = $user->school_id;
            $studentCount = Student::where('school_id', $school_id)->count();
            $teacherCount = Teacher::where('school_id', $school_id)->count();
            $classCount = SchoolClass::where('school_id', $school_id)->count();
            $schoolCount = 1; // They only see their own school
            $schoolName = $user->school->name ?? 'Your School';
            // Scope financial queries to the school
            $financialQuery->where('school_id', $school_id);
        }

        // --- Financial KPIs ---
        $allVouchers = $financialQuery->get();

        $revenueToday = $allVouchers
            ->whereIn('status', ['paid', 'partial'])
            ->where('paid_at', '>=', Carbon::today())
            ->sum('amount_paid');

        $totalPending = $allVouchers
            ->whereIn('status', ['pending', 'partial'])
            ->sum(function($voucher) {
                return $voucher->amount_due - $voucher->amount_paid;
            });
        
        $totalRevenue = $allVouchers
            ->whereIn('status', ['paid', 'partial'])
            ->sum('amount_paid');


        return view('admin.dashboard', compact(
            'studentCount', 
            'teacherCount', 
            'classCount', 
            'schoolCount', 
            'schoolName', 
            'revenueToday', 
            'totalPending',
            'totalRevenue'
        ));
    }

    /**
     * Data and view for Teachers.
     */
    private function teacherDashboard(): View
    {
        $user = Auth::user();
        // This assumes a 'teacher' relationship on the User model that links to the Teacher model.
        $teacher = Teacher::where('user_id', $user->id)->first();
        $todaySchedules = collect();
        $classCount = 0;
        $subjectCount = 0;

        if ($teacher) {
            $today = strtolower(Carbon::now()->format('l')); // e.g., 'monday'
            $todaySchedules = $teacher->schedules()->where('day_of_week', $today)->with(['schoolClass', 'subject'])->get();
            
            // Get unique classes and subjects the teacher is assigned to
            $classCount = $teacher->schedules()->distinct('school_class_id')->count('school_class_id');
            $subjectCount = $teacher->schedules()->distinct('subject_id')->count('subject_id');
        }

        return view('admin.teacher_dashboard', compact('todaySchedules', 'classCount', 'subjectCount'));
    }

    /**
     * View for other staff members.
     */
    private function staffDashboard(): View
    {
        return view('admin.staff_dashboard');
    }
}

