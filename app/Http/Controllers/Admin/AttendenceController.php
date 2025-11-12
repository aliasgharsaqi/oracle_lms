<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Carbon ko import karein
use Carbon\CarbonPeriod;

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
            'action' => 'required|in:check_in,check_out,absent,late_arrival',
        ]);

        $school_id = Auth::user()->school_id;
        $admin_id = Auth::id();
        $today = today(); // Ye function SIRF aaj ke liye hai

        $teacher = Teacher::where('id', $request->teacher_id)
                          ->where('school_id', $school_id)
                          ->firstOrFail();

        $attendance = Attendance::firstOrNew([
            'teacher_id' => $teacher->id,
            'date' => $today,
        ]);

        if (!$attendance->exists) {
            $attendance->school_id = $school_id;
            $attendance->marked_by = $admin_id;
        }

        switch ($request->action) {
            case 'check_in':
                $attendance->status = 'present';
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
                $attendance->notes = 'Marked absent by admin.';
                break;

            case 'late_arrival':
                $attendance->status = 'late_arrival';
                if (!$attendance->check_in) { 
                    $attendance->check_in = now(); // Timestamp set karein
                }
                $attendance->notes = 'Marked late by admin.';
                break;
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'status' => $attendance->status,
        ]);
    }

    /**
     * AAJ (Today) ke liye leave apply karein.
     */
    public function apply_leave(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'leave_type' => 'required|in:leave,short_leave',
            'reason' => 'required|string|min:5',
        ]);

        $school_id = Auth::user()->school_id;
        $admin_id = Auth::id();
        $today = today(); // Ye function SIRF aaj ke liye hai

        $teacher = Teacher::where('id', $request->teacher_id)
                          ->where('school_id', $school_id)
                          ->firstOrFail();

        $attendance = Attendance::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'date' => $today,
            ],
            [
                'school_id' => $school_id,
                'status' => $request->leave_type,
                'notes' => $request->reason,
                'marked_by' => $admin_id,
                'check_in' => null, 
                'check_out' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'status' => $attendance->status,
        ]);
    }

    /**
     * NAYA FUNCTION: Guzishta (Past) attendance ko update karein.
     */
    public function update_past_attendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'action' => 'required|in:present,absent,leave,short_leave,late_arrival',
            'date' => 'required|date_format:Y-m-d',
            'reason' => 'nullable|string|min:5|required_if:action,leave,short_leave',
        ]);

        $school_id = Auth::user()->school_id;
        $admin_id = Auth::id();
        $selected_date = Carbon::parse($request->date);

        // Security: Is function se aaj ki ya future ki date edit nahi ho sakti
        if ($selected_date->isToday() || $selected_date->isFuture()) {
            return response()->json(['error' => 'Cannot edit today or a future date with this method.'], 422);
        }

        $teacher = Teacher::where('id', $request->teacher_id)
                          ->where('school_id', $school_id)
                          ->firstOrFail();

        // Sirf status update karein, koi timestamp (check_in/out) nahi
        $attendance = Attendance::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'date' => $selected_date,
            ],
            [
                'school_id' => $school_id,
                'status' => $request->action,
                'marked_by' => $admin_id,
                'notes' => $request->reason ?? 'Updated by admin',
                'check_in' => null, // Manual override pe timestamps clear karein
                'check_out' => null,
            ]
        );

        return response()->json(['success' => true, 'status' => $attendance->status]);
    }

    public function monthly_report(Request $request)
    {
        $school_id = Auth::user()->school_id;

        // Mahina (Month) aur Saal (Year) request se lein, warna default aaj ka mahina
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        
        try {
            $startDate = Carbon::parse($selectedMonth)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        } catch (\Exception $e) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
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
}