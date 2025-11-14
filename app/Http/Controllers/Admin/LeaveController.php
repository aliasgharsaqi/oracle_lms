<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance; // <-- Model ka naam StudentAttendance hai
use App\Models\LeaveApplication;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Admin: Show all pending leave requests.
     */
    public function index()
    {
        $this->authorize('viewAny', LeaveApplication::class); // Policy assume kar raha hoon
        
        $pendingLeaves = LeaveApplication::with('student.user')
                            ->where('status', 'pending')
                            ->orderBy('date', 'desc')
                            ->get();
                            
        return view('admin.leave.index', compact('pendingLeaves')); // View aapko banana hoga
    }

    /**
     * Student: Show leave application form.
     */
    public function create()
    {
        // Yeh view 'attendence.blade.php' mein modal ke zariye handle ho raha hai
        // Lekin agar dedicated page hai, to:
        return view('student.leave.create'); // User provided create.blade.php
    }

    /**
     * Student: Store leave application.
     * REQ 1.1: Handles multiple dates from flatpickr.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dates'       => 'required|string', // Flatpickr se 'YYYY-MM-DD, YYYY-MM-DD'
            'leave_type'  => 'required|in:full_day,short_leave',
            'reason'      => 'nullable|string|max:500',
        ]);

        // Assuming student is logged in
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // REQ 1.1: Dates ko 'range' ke bajaye 'multiple' se handle karein
        $datesArray = explode(', ', $request->dates);
        $createdCount = 0;

        foreach ($datesArray as $dateStr) {
            $date = Carbon::parse($dateStr)->format('Y-m-d');

            // Check karein kahin is date ki pehle se leave toh nahi
            $existing = LeaveApplication::where('student_id', $student->id)
                ->where('date', $date)
                ->first();

            if (! $existing) {
                LeaveApplication::create([
                    'student_id' => $student->id,
                    'date'       => $date,
                    'leave_type' => $request->leave_type,
                    'reason'     => $request->reason,
                    'status'     => 'pending',
                ]);
                $createdCount++;
            }
        }

        if ($createdCount == 0) {
            return redirect()->back()->with('error', 'Leave application for these dates already exists.');
        }

        return redirect()->back()->with('success', "$createdCount leave application(s) submitted successfully.");
    }

    /**
     * Admin: Approve a leave application.
     * REQ 1.4: Auto check-out for short leave.
     * REQ 1.6: Handles approval.
     */
    public function approve(Request $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $this->authorize('update', $leave); // Policy

        // 1. Leave application ko approve karein
        $leave->update([
            'status' => 'approved',
            'approved_by_admin_id' => Auth::id(),
        ]);

        // 2. Attendance record ko update/create karein
        $attendance = StudentAttendance::firstOrNew(
            [
                'student_id' => $leave->student_id,
                'attendance_date' => $leave->date,
            ],
            [
                'school_id' => $leave->student->school_id, // Student model se get karein
                'school_class_id' => $leave->student->school_class_id,
            ]
        );

        if ($leave->leave_type == 'full_day') {
            $attendance->status = 'leave';
            $attendance->check_in = null;
            $attendance->check_out = null;
            $attendance->remarks = $leave->reason ?? 'Approved Full Day Leave';
        } else {
            // REQ 1.4: Short Leave Logic
            // User stub ke mutabiq status 'present' rakhein
            $attendance->status = 'present'; 
            $attendance->remarks = $leave->reason ?? 'Approved Short Leave';
            
            // Agar check_in nahi hai to set karein (e.g., 8:00 AM)
            if (! $attendance->check_in) {
                $attendance->check_in = '08:00:00'; // Default start time
            }
            // Check_out set karein (e.g., 12:00 PM)
            $attendance->check_out = '12:00:00'; // REQ 1.4
        }
        
        $attendance->save();

        return redirect()->back()->with('success', 'Leave approved and attendance updated.');
    }

    /**
     * Admin: Reject a leave application.
     */
    public function reject(Request $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $this->authorize('update', $leave);

        $leave->update([
            'status' => 'rejected',
            'approved_by_admin_id' => Auth::id(),
        ]);

        // Optional: Agar leave reject ho to attendance ko 'absent' mark karein?
        // Yeh business logic par depend karta hai. Filhal sirf leave reject kar rahe hain.

        return redirect()->back()->with('success', 'Leave application rejected.');
    }
}