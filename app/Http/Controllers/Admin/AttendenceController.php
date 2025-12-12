<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Teacher;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendenceController extends Controller
{
    /**
     * Attendance Page Display
     */
    // App/Http/Controllers/Admin/AttendenceController.php

    public function teacher_attendence(Request $request)
    {
        $school_id = Auth::user()->school_id;

        try {
            $selected_date = Carbon::parse($request->input('date', today()));
        } catch (\Exception $e) {
            $selected_date = today();
        }

        // --- NEW: Database se School ki Timing lein ---
        $school = School::find($school_id);

        // Agar URL mein time hai to wo lein, warna Database wala, warna Default
        $dbStartTime = $school->start_time ? Carbon::parse($school->start_time)->format('H:i') : '08:00';
        $dbEndTime   = $school->end_time ? Carbon::parse($school->end_time)->format('H:i') : '14:00';

        $schoolStartTime = $request->input('school_start_time', $dbStartTime);
        $schoolEndTime   = $request->input('school_end_time', $dbEndTime);

        $teachers = Teacher::with(['user', 'attendanceRecord' => function ($query) use ($selected_date) {
            $query->where('date', $selected_date->toDateString());
        }])
            ->where('school_id', $school_id)
            ->get();

        return view('admin.attendence.teacher', compact('teachers', 'selected_date', 'schoolStartTime', 'schoolEndTime'));
    }

    public function save_time_settings(Request $request)
    {
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        $school = School::find(Auth::user()->school_id);
        $school->start_time = $request->start_time;
        $school->end_time = $request->end_time;
        $school->save();

        return response()->json(['success' => true, 'message' => 'Time settings saved permanently!']);
    }

    /**
     * Mark Attendance (Live Check In with Auto-Late Calculation)
     */
    public function mark_attendance(Request $request)
    {
        $request->validate([
            'teacher_id'        => 'required|exists:teachers,id',
            'action'            => 'required|in:check_in,check_out,absent,short_leave,leave',
            'school_start_time' => 'nullable', // Expected format HH:mm e.g., "08:00"
        ]);

        $inputStartTime = $request->input('school_start_time');
        $school_id = Auth::user()->school_id;
        $admin_id  = Auth::id();
        $today     = today();

        if(!$inputStartTime) {
             $school = School::find(Auth::user()->school_id);
             $inputStartTime = $school->start_time ? Carbon::parse($school->start_time)->format('H:i') : '08:00';
        }
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

        // Get the School Start Time from Input (Default to 08:00 AM if empty)
        $inputStartTime = $request->input('school_start_time', '08:00');

        switch ($request->action) {
            case 'check_in':
                $now                  = now(); // Current Time
                $attendance->check_in = $now;

                // --- AUTO LATE CALCULATION LOGIC ---
                // Create a Carbon instance for Today + Input Time
                $schoolStartDateTime = Carbon::parse($today->format('Y-m-d') . ' ' . $inputStartTime);

                // Compare: If NOW is greater than School Start Time
                if ($now->gt($schoolStartDateTime)) {
                    $attendance->status = 'late_arrival';
                    // Calculate difference in minutes
                    $attendance->late_minutes = $now->diffInMinutes($schoolStartDateTime);
                    $attendance->notes        = "Auto-marked late (Arrived at " . $now->format('h:i A') . ")";
                } else {
                    $attendance->status       = 'present';
                    $attendance->late_minutes = 0;
                    $attendance->notes        = "On time";
                }
                break;

            case 'check_out':
                if ($attendance->check_in) {
                    $attendance->check_out = now();
                } else {
                    return response()->json(['error' => 'Must Check In before Checking Out.'], 422);
                }
                break;

            case 'absent':
                $attendance->status       = 'absent';
                $attendance->late_minutes = 0;
                $attendance->notes        = 'Marked absent by admin.';
                break;
        }

        $attendance->save();

        return response()->json([
            'success'      => true,
            'status'       => $attendance->status,
            'late_minutes' => $attendance->late_minutes,
        ]);
    }

    /**
     * Update Past Attendance (Manual Edits)
     */
    public function update_past_attendance(Request $request)
    {
        $request->validate([
            'teacher_id'   => 'required|exists:teachers,id',
            'action'       => 'required|in:present,absent,leave,short_leave,late_arrival',
            'date'         => 'required|date_format:Y-m-d',
            'reason'       => 'nullable|string|min:5|required_if:action,leave,short_leave',
            'late_minutes' => 'nullable|integer|min:1',
        ]);

        $school_id     = Auth::user()->school_id;
        $admin_id      = Auth::id();
        $selected_date = Carbon::parse($request->date);

        if ($selected_date->isToday() || $selected_date->isFuture()) {
            return response()->json(['error' => 'Cannot edit today or future via this method.'], 422);
        }

        $teacher = Teacher::where('id', $request->teacher_id)
            ->where('school_id', $school_id)
            ->firstOrFail();

        $attendance = Attendance::firstOrNew([
            'teacher_id' => $teacher->id,
            'date'       => $selected_date,
        ]);

        if (! $attendance->exists) {
            $attendance->school_id = $school_id;
            $attendance->marked_by = $admin_id;
        }

        // Handle Leaves
        if ($request->action == 'leave' || $request->action == 'short_leave') {
            $attendance->status       = 'absent';
            $attendance->leave_type   = $request->action;
            $attendance->leave_status = 'pending';
            $attendance->notes        = $request->reason ?? 'Admin Request';
            $attendance->check_in     = null;
            $attendance->check_out    = null;
            $attendance->late_minutes = 0;
        } else {
            // Handle Present/Absent/Late
            $attendance->status = $request->action;
            $attendance_notes   = $request->reason ?? 'Updated by admin';

            if ($request->action == 'late_arrival') {
                // For past dates, we trust the manual input or default to 0
                $attendance->late_minutes = $request->late_minutes ?? 0;
                $attendance_notes         = "Marked Late manually ({$attendance->late_minutes} mins)";
            } else {
                $attendance->late_minutes = 0;
            }

            // Clear leave status
            $attendance->leave_type   = null;
            $attendance->leave_status = null;
            $attendance->notes        = $attendance_notes;
            $attendance->check_in     = null;
            $attendance->check_out    = null;
        }

        $attendance->save();

        return response()->json(['success' => true]);
    }

    /**
     * Apply Leave (Today)
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

        $attendance = Attendance::firstOrNew(['teacher_id' => $teacher->id, 'date' => $today]);

        if (! $attendance->exists) {
            $attendance->school_id = $school_id;
            $attendance->marked_by = $admin_id;
            $attendance->status    = 'absent';
        }

        $attendance->leave_type   = $request->leave_type;
        $attendance->leave_status = 'pending';
        $attendance->notes        = $request->reason;
        $attendance->check_in     = null;
        $attendance->check_out    = null;
        $attendance->late_minutes = 0;

        $attendance->save();

        return response()->json(['success' => true]);
    }

    // Monthly Report & Pending Leaves (Standard)
    public function monthly_report(Request $request)
    {
        $school_id     = Auth::user()->school_id;
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        try {
            $startDate = Carbon::parse($selectedMonth)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();
        } catch (\Exception $e) {
            $startDate = now()->startOfMonth();
            $endDate   = now()->endOfMonth();
        }
        $dates       = CarbonPeriod::create($startDate, $endDate);
        $teachers    = Teacher::with('user')->where('school_id', $school_id)->get();
        $attendances = Attendance::where('school_id', $school_id)
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        $attendanceMatrix = $attendances->groupBy('teacher_id')
            ->map(function ($teacherAttendances) {
                return $teacherAttendances->keyBy(function ($att) {
                    return $att->date->format('Y-m-d');
                });
            });
        return view('admin.attendence.monthly_report', compact('teachers', 'dates', 'selectedMonth', 'attendanceMatrix'));
    }

    public function show_pending_leaves(Request $request)
    {
        $school_id      = Auth::user()->school_id;
        $pending_leaves = Attendance::with('teacher.user')->where('school_id', $school_id)->where('leave_status', 'pending')->orderBy('date', 'desc')->get();
        return view('admin.attendence.pending_leaves', compact('pending_leaves'));
    }

    public function action_on_leave(Request $request)
    {
        $request->validate(['attendance_id' => 'required', 'action' => 'required|in:approve,reject']);
        $att = Attendance::findOrFail($request->attendance_id);
        if ($request->action == 'approve') {
            $att->leave_status = 'approved';
            $att->status       = $att->leave_type;
        } else {
            $att->leave_status = 'rejected';
            $att->status       = 'absent';
            $att->notes .= ' (Rejected)';
        }
        $att->save();
        return response()->json(['success' => true]);
    }
}
