<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request; // <-- Make sure this is imported
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    /**
     * Show the view to take/mark attendance.
     * This method now ALSO handles fetching students if a class is selected.
     */
    public function create(Request $request)
    {
        $this->authorize('viewAny', StudentAttendance::class);

        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        
        $selectedClassId = $request->input('school_class_id');
        $selectedDate = $request->input('attendance_date', now()->format('Y-m-d'));
        
        $students = collect();

        if ($selectedClassId) {
            $students = Student::where('school_class_id', $selectedClassId)
                ->with(['user', 'attendances' => function ($query) use ($selectedDate) {
                    $query->where('attendance_date', $selectedDate);
                }, 'leaveRequests' => function ($query) use ($selectedDate) {
                    $query->where('start_date', '<=', $selectedDate)
                          ->where('end_date', '>=', $selectedDate)
                          ->where('status', 'pending');
                }])
                ->get();
        }

        return view('admin.students.attendence', compact(
            'classes',
            'students',
            'selectedClassId',
            'selectedDate'
        ));
    }

    /**
     * Fetch students for a given class and date.
     * THIS METHOD IS NO LONGER USED AND CAN BE DELETED.
     * The logic is now inside the create() method.
     */
    public function fetchStudents(Request $request)
    {
        // ... (THIS ENTIRE METHOD CAN BE DELETED)
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
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);

        $this->authorize('create', StudentAttendance::class);

        $classId = $request->input('school_class_id');
        $date = Carbon::parse($request->input('attendance_date'))->format('Y-m-d');
        $schoolId = auth()->user()->school_id;

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $attn) {
                // ... (updateOrCreate logic)
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $attn['student_id'],
                        'attendance_date' => $date,
                    ],
                    [
                        'school_id' => $schoolId,
                        'school_class_id' => $classId,
                        'status' => $attn['status'],
                        'remarks' => $attn['remarks'] ?? null,
                    ]
                );
            }
            DB::commit();
            
            return redirect()->route('attendance.create', [
                'school_class_id' => $classId,
                'attendance_date' => $date
            ])->with('success', 'Attendance saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    /**
     * Show the attendance report view.
     */
    public function report()
    {
        $this->authorize('viewAny', StudentAttendance::class); 
        
        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        
        // *** BUG FIX ***
        // Path ko 'admin.students.attendence.report' se 'admin.students.report' karein
        return view('admin.students.report', compact('classes'));
    }

    /**
     * Fetch and show the attendance report.
     */
    public function showReport(Request $request)
    {
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

        // *** BUG FIX ***
        // Path ko 'admin.students.attendence.report' se 'admin.students.report' karein
        return view('admin.students.report', compact(
            'classes', 
            'students', 
            'attendances', 
            'daysInMonth', 
            'dateRange', 
            'schoolClass', 
            'carbonMonth'
        ));
    }
}