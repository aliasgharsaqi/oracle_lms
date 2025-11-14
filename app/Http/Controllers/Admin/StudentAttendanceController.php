<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance; // Iska model StudentAttendance hai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    /**
     * Show the view to take/mark attendance.
     */
    public function create()
    {
        $this->authorize('create', StudentAttendance::class);

        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        
        // View path (jaisa aapne file mein provide kiya)
        return view('admin.students.attendence', compact('classes')); 
    }

    /**
     * Fetch students for a given class and date.
     */
    public function fetchStudents(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'attendance_date' => 'required|date',
        ]);

        $this->authorize('create', StudentAttendance::class);

        $classId = $request->input('school_class_id');
        $date = Carbon::parse($request->input('attendance_date'))->format('Y-m-d');

        $students = Student::with(['user', 'attendances' => function ($query) use ($date) {
            $query->where('attendance_date', $date);
        }])
        ->where('school_class_id', $classId)
        ->whereHas('user', function ($query) {
            $query->where('status', 1); // Assuming 1 is 'active'
        })
        ->orderBy('id_card_number') 
        ->get();

        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        $selectedClassId = $classId;
        $selectedDate = $date;

        // View path (jaisa aapne file mein provide kiya)
        return view('admin.students.attendence', compact('classes', 'students', 'selectedClassId', 'selectedDate'));
    }

    /**
     * Store the attendance data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave',
        ]);

        $this->authorize('create', StudentAttendance::class);

        DB::beginTransaction();
        try {
            $schoolId = auth()->user()->school_id;
            $classId = $request->input('school_class_id');
            $date = Carbon::parse($request->input('attendance_date'))->format('Y-m-d');

            foreach ($request->input('attendance') as $attnData) {
                
                // REQ 2.1: Auto Check-in on 'Late'
                $checkInTime = null;
                if ($attnData['status'] == 'late') {
                    // Check karein agar pehle se check_in nahi hai (optional)
                    $existing = StudentAttendance::where('student_id', $attnData['student_id'])
                                                 ->where('attendance_date', $date)
                                                 ->first();
                    
                    // Agar pehle se check_in hai to usay retain karein, warna naya time set karein
                    $checkInTime = $existing->check_in ?? now()->format('H:i:s');
                }

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $attnData['student_id'],
                        'attendance_date' => $date,
                    ],
                    [
                        'school_class_id' => $classId,
                        'school_id' => $schoolId,
                        'status' => $attnData['status'],
                        'remarks' => $attnData['remarks'] ?? null,
                        'check_in' => $checkInTime, // REQ 2.1 Applied
                        'check_out' => null, // updateOrCreate mein default set karein
                    ]
                );
            }

            DB::commit();
            
            // REQ 3.2: Routing Fix
            // 'admin.students.attendance' ki jagah 'attendance.create' (jo web.php mein defined hai)
            return redirect()->route('attendance.create')->with('success', 'Attendance marked successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark attendance. Error: ' . $e->getMessage())->withInput();
        }
    }

    // ... report() aur showReport() methods (jaisa aapne provide kiya) ...
    public function report()
    {
        $this->authorize('viewAny', StudentAttendance::class);
        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        return view('admin.students.report', compact('classes'));
    }

    public function showReport(Request $request)
    {
        // ... Aapka poora existing code ...
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $this->authorize('viewAny', StudentAttendance::class);

        $classId = $request->input('school_class_id');
        $month = $request->input('month');
        $carbonMonth = Carbon::parse($month);
        $startDate = $carbonMonth->copy()->startOfMonth();
        $endDate = $carbonMonth->copy()->endOfMonth();

        $students = Student::with('user')
            ->where('school_class_id', $classId)
            ->whereHas('user', fn($q) => $q->where('status', 1))
            ->orderBy('id_card_number')
            ->get();
            
        $attendances = StudentAttendance::where('school_class_id', $classId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id')
            ->map(function ($group) {
                return $group->keyBy(fn($item) => $item->attendance_date->format('Y-m-d'));
            });

        $daysInMonth = $startDate->daysInMonth;
        $dateRange = range(1, $daysInMonth);
        $schoolClass = SchoolClass::find($classId);
        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();

        return view('admin.students.report', compact(
            'classes', 
            'students', 
            'attendances', 
            'daysInMonth', 
            'dateRange',
            'carbonMonth', 
            'schoolClass',
            'month'
        ));
    }
}