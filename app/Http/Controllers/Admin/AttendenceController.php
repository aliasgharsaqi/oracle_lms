<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request; // Carbon ko import karein
use Illuminate\Support\Facades\Auth;

class AttendenceController extends Controller
{
    /**
     * Attendance page ko data ke sath display karein.
     */
    public function teacher_attendence(Request $request)
    {
        $school_id = Auth::user()->school_id;

        try {
            $selected_date = Carbon::parse($request->input('date', today()));
        } catch (\Exception $e) {
            $selected_date = today();
        }

        // Selected date ke hisab se attendance record load karein
        $teachers = Teacher::with(['user', 'attendanceRecord' => function ($query) use ($selected_date) {
            $query->where('date', $selected_date->toDateString());
        }])
            ->where('school_id', $school_id)
            ->get();

        return view('admin.attendence.teacher', compact('teachers', 'selected_date'));
    }

    /**
     * AAJ (Today) ki attendance mark karein (Check In, Out, Absent, Late).
     */
    public function mark_attendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'action'     => 'required|in:check_in,check_out,absent,late_arrival',
        ]);

        $school_id = Auth::user()->school_id;
        $admin_id  = Auth::id();
        $today     = today(); // Ye function SIRF aaj ke liye hai

        $teacher = Teacher::where('id', $request->teacher_id)
            ->where('school_id', $school_id)
            ->firstOrFail();

        $attendance = Attendance::firstOrNew([
            'teacher_id' => $teacher->id,
            'date'       => $today,
        ]);

        if (! $attendance->exists) {
            $attendance->school_id = $school_id;
            $attendance->marked_by = $admin_id;
        }

        switch ($request->action) {
            case 'check_in':
                $attendance->status   = 'present';
                $attendance->check_in = now(); // Timestamp set karein
                break;

            case 'check_out':
                if ($attendance->check_in) {
                    $attendance->check_out = now(); // Timestamp set karein
                } else {
                    return response()->json(['error' => 'Must Check In before Checking Out.'], 422);
                }
                break;

            case 'absent':
                $attendance->status = 'absent';
                $attendance->notes  = 'Marked absent by admin.';
                break;

            case 'late_arrival':
                $attendance->status = 'late_arrival';
                if (! $attendance->check_in) {
                    $attendance->check_in = now(); // Timestamp set karein
                }
                $attendance->notes = 'Marked late by admin.';
                break;
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'status'  => $attendance->status,
        ]);
    }

    /**
     * AAJ (Today) ke liye leave request karein.
     */
    public function apply_leave(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'leave_type' => 'required|in:leave,short_leave',
            'reason'     => 'required|string|min:5',
        ]);

        $school_id = Auth::user()->school_id;
        $admin_id  = Auth::id();
        $today     = today();

        $teacher = Teacher::where('id', $request->teacher_id)
            ->where('school_id', $school_id)
            ->firstOrFail();

        // Check if attendance already exists
        $attendance = Attendance::firstOrNew(
            [
                'teacher_id' => $teacher->id,
                'date'       => $today,
            ]
        );

        // Don't change the main status yet. Mark it as 'absent' by default.
        if (! $attendance->exists) {
            $attendance->school_id = $school_id;
            $attendance->status    = 'absent'; // Mark as absent until approved
            $attendance->marked_by = $admin_id;
        }

                                                          // Set leave-specific details
        $attendance->leave_type   = $request->leave_type; // We should add this column! (See note below)
        $attendance->leave_status = 'pending';            // NEW
        $attendance->notes        = $request->reason;     // Reason
        $attendance->check_in     = null;
        $attendance->check_out    = null;

        $attendance->save();

        return response()->json([
            'success'      => true,
            'status'       => $attendance->status,
            'leave_status' => $attendance->leave_status, // Send back new status
        ]);
    }

    public function update_past_attendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'action'     => 'required|in:present,absent,leave,short_leave,late_arrival',
            'date'       => 'required|date_format:Y-m-d',
            'reason'     => 'nullable|string|min:5|required_if:action,leave,short_leave',
        ]);

        $school_id     = Auth::user()->school_id;
        $admin_id      = Auth::id();
        $selected_date = Carbon::parse($request->date);

        if ($selected_date->isToday() || $selected_date->isFuture()) {
            return response()->json(['error' => 'Cannot edit today or a future date with this method.'], 422);
        }

        $teacher = Teacher::where('id', $request->teacher_id)
            ->where('school_id', $school_id)
            ->firstOrFail();

        // Get the existing record or create a new one
        $attendance = Attendance::firstOrNew(
            [
                'teacher_id' => $teacher->id,
                'date'       => $selected_date,
            ]
        );

        if (! $attendance->exists) {
            $attendance->school_id = $school_id;
            $attendance->marked_by = $admin_id;
        }

        // Handle Leave requests separately
        if ($request->action == 'leave' || $request->action == 'short_leave') {

            $attendance->status       = 'absent';         // Mark as absent until approved
            $attendance->leave_type   = $request->action; // Store 'leave' or 'short_leave'
            $attendance->leave_status = 'pending';        // Set to pending
            $attendance->notes        = $request->reason ?? 'Leave request submitted by admin';
            $attendance->check_in     = null;
            $attendance->check_out    = null;

        } else {
            // Handle Present, Absent, Late

            $attendance->status = $request->action;
            $attendance_notes   = $request->reason ?? 'Updated by admin';

            // Clear leave status if we are marking as present/absent
            if ($request->action == 'present' || $request->action == 'absent' || $request->action == 'late_arrival') {
                $attendance->leave_type   = null;
                $attendance->leave_status = null;
                $attendance_notes         = 'Updated to ' . $request->action . ' by admin';
            }

            $attendance->notes     = $attendance_notes;
            $attendance->check_in  = null;
            $attendance->check_out = null;
        }

        $attendance->save();

        return response()->json([
            'success'      => true,
            'status'       => $attendance->status,
            'leave_status' => $attendance->leave_status,
        ]);
    }

    public function monthly_report(Request $request)
    {
        $school_id = Auth::user()->school_id;

        // Mahina (Month) aur Saal (Year) request se lein, warna default aaj ka mahina
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        try {
            $startDate = Carbon::parse($selectedMonth)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();
        } catch (\Exception $e) {
            $startDate = now()->startOfMonth();
            $endDate   = now()->endOfMonth();
        }

        // Mahine ke tamam din (dates) generate karein
        $dates = CarbonPeriod::create($startDate, $endDate);

        // School ke tamam teachers
        $teachers = Teacher::with('user')
            ->where('school_id', $school_id)
            ->get();

        // Tamam teachers ki poore mahine ki attendance ek hi query mein lein
        $attendances = Attendance::where('school_id', $school_id)
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Data ko table ke liye tayyar karein (Matrix/Pivot)
        // Format: [teacher_id => [date => status, ...]]
        $attendanceMatrix = $attendances->groupBy('teacher_id')
            ->map(function ($teacherAttendances) {
                return $teacherAttendances->keyBy(function ($att) {
                    return $att->date->format('Y-m-d'); // Date ko key banayein
                });
            });

        return view('admin.attendence.monthly_report', compact(
            'teachers',
            'dates',
            'selectedMonth',
            'attendanceMatrix'
        ));
    }

    // app/Http/Controllers/Admin/AttendenceController.php

    /**
     * Show all pending leave requests.
     */
    public function show_pending_leaves(Request $request)
    {
        $school_id = Auth::user()->school_id;

        $pending_leaves = Attendance::with('teacher.user')
            ->where('school_id', $school_id)
            ->where('leave_status', 'pending')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.attendence.pending_leaves', compact('pending_leaves'));
    }

    /**
     * Approve or Reject a leave request.
     */
    public function action_on_leave(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'action'        => 'required|in:approve,reject',
        ]);

        $school_id = Auth::user()->school_id;

        $attendance = Attendance::where('id', $request->attendance_id)
            ->where('school_id', $school_id)
            ->firstOrFail();

        if ($request->action == 'approve') {
            $attendance->leave_status = 'approved';
            // Now we set the official status to 'leave' or 'short_leave'
            $attendance->status = $attendance->leave_type ?? 'leave';
        } else {
            $attendance->leave_status = 'rejected';
            // If rejected, they remain marked 'absent'
            $attendance->status = 'absent';
            $attendance->notes  = ($attendance->notes ?? '') . ' (Leave Rejected)';
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Leave ' . $request->action . 'd.',
        ]);
    }
}
