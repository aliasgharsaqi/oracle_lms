<?php
// app/Http/Controllers/Admin/StudentLeaveController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class StudentLeaveController extends Controller
{
    /**
     * For Students: Show their own leave requests.
     */
    public function index()
    {
        // We assume the authenticated user is a student and has a 'student' relationship
        $student = Auth::user()->student; // This assumes you have this relationship set up on the User model.
        // If not, use: $student = Student::where('user_id', Auth::id())->firstOrFail();

        $leaveRequests = StudentLeaveRequest::where('student_id', $student->id)
            ->orderBy('start_date', 'desc')
            ->get();
            
        // You will need to create this view: resources/views/student/leaves/index.blade.php
        return view('student.leaves.index', compact('leaveRequests'));
    }

    /**
     * For Students: Store their leave application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|in:full_day,short_leave',
            'reason' => 'required|string|min:10',
        ]);

        // If it's a short leave, end_date must be the same as start_date.
        if ($request->leave_type == 'short_leave' && $request->start_date != $request->end_date) {
            return back()->with('error', 'Short leave must be for a single day.');
        }

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        StudentLeaveRequest::create([
            'student_id' => $student->id,
            'school_id' => $student->school_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'leave_type' => $request->leave_type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // This assumes you have a student dashboard route
        return redirect()->route('student.leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    /**
     * For Admins/Teachers: Show all pending leave requests.
     */
    public function adminIndex()
    {
        $this->authorize('viewAny', StudentAttendance::class); // Re-using existing policy

        // *** FIX: Renamed variable to '$leaveRequests' to match the view ***
       $pending_leaves = StudentLeaveRequest::with('student.user', 'student.schoolClass')
    ->where('school_id', Auth::user()->school_id)
    ->where('status', 'pending')
    ->orderBy('start_date', 'asc')
    ->get();

// Pass the '$pending_leaves' variable to the view
return view('admin.leaves.student_pending', compact('pending_leaves'));
    }

    /**
     * For Admins/Teachers: Action on a leave request.
     */
  /**
     * For Admins/Teachers: Action on a leave request.
     */
    public function actionOnLeave(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:student_leave_requests,id',
            'action' => 'required|in:approve,reject',
        ]);

        $leaveRequest = StudentLeaveRequest::with('student')->findOrFail($request->request_id);
        
        if ($leaveRequest->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $leaveRequest->update([
            'status' => $request->action == 'approve' ? 'approved' : 'rejected',
            'action_by_user_id' => Auth::id(),
            'action_at' => now(),
        ]);

        // If approved, create the attendance records
        if ($request->action == 'approve') {
            $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);
            $student = $leaveRequest->student;
            // $now = Carbon::now(); // Ye line ab zaroori nahi rahi agar check_out save nahi karna

            foreach ($period as $date) {
                $status = ($leaveRequest->leave_type == 'full_day') ? 'leave' : 'present';
                
                // Ye line remove kar dein kyunki column nahi hai
                // $check_out = ($leaveRequest->leave_type == 'short_leave') ? $now : null; 

                $leave_type = ($leaveRequest->leave_type == 'short_leave') ? 'short_leave' : 'full_day';

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'attendance_date' => $date->format('Y-m-d'),
                    ],
                    [
                        'school_id' => $student->school_id,
                        'school_class_id' => $student->school_class_id,
                        'status' => $status,
                        // 'check_out' => $check_out,
                        // 'remarks' => $leaveRequest->reason,
                        // 'leave_type' => $leave_type,
                        // 'leave_status' => 'approved',
                    ]
                );
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Leave ' . $request->action . 'd.']);
    }


    public function adminStore(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|in:full_day,short_leave',
            'reason' => 'required|string|min:10',
        ]);

        // If it's a short leave, end_date must be the same as start_date.
        if ($request->leave_type == 'short_leave' && $request->start_date != $request->end_date) {
            // *** CHANGED: Return JSON error ***
            return response()->json(['message' => 'Short leave must be for a single day.'], 422);
        }

        $student = Student::findOrFail($request->student_id);

        // Authorize (check school)
        if ($student->school_id !== \Illuminate\Support\Facades\Auth::user()->school_id) {
            // *** CHANGED: Return JSON error ***
            return response()->json(['message' => 'This student does not belong to your school.'], 403);
        }

        \App\Models\StudentLeaveRequest::create([
            'student_id' => $student->id,
            'school_id' => $student->school_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'leave_type' => $request->leave_type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // *** CHANGED: Return JSON success ***
        return response()->json([
            'success' => true,
            'message' => 'Leave request for ' . $student->user->name . ' submitted successfully.'
        ]);
    }
}            