<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        // The SchoolScope on the model automatically handles filtering for School Admins.
        // We only need specific logic for Teachers.
        $query = TeacherSchedule::with(['teacher.user', 'schoolClass', 'subject']);

        if ($user->hasRole('Teacher')) {
            if ($user->teacher) {
                $query->where('teacher_id', $user->teacher->id);
            } else {
                $query->whereRaw('1 = 0'); // No teacher profile, show no schedules.
            }
        }

        // Apply filters from the request
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $schedules = $query->orderBy(DB::raw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')"))
            ->orderBy('start_time')
            ->get();

        // Dropdowns are correctly scoped based on the logged-in admin.
        $teachers = Teacher::whereHas('user', fn($q) => $q->where('school_id', $user->school_id))->with('user')->get();
        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        return view('admin.schedules.index', compact('schedules', 'teachers', 'classes'));
    }

    public function create(): View
    {
        $school_id = Auth::user()->school_id;
        $teachers = Teacher::whereHas('user', fn($q) => $q->where('school_id', $school_id))->with('user')->get();
        $classes = SchoolClass::where('school_id', $school_id)->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.schedules.create', compact('teachers', 'classes', 'days'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'day_of_week' => ['required', 'array', 'min:1'],
            'day_of_week.*' => ['string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        foreach ($request->day_of_week as $day) {
            TeacherSchedule::create([
                'school_id' => Auth::user()->school_id,
                'teacher_id' => $request->teacher_id,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'day_of_week' => $day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);
        }

        return redirect()->route('schedules.index')->with('success', 'Lecture(s) scheduled successfully.');
    }

    public function show($id): View|RedirectResponse
    {
        $initialSchedule = TeacherSchedule::findOrFail($id);

        if (!$this->authorizeTeacherAccess($initialSchedule)) {
            return redirect()->route('schedules.index')->with('error', 'You are not authorized to access this schedule.');
        }

        $teacher = $initialSchedule->teacher;

        $allSchedules = TeacherSchedule::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'subject'])
            ->orderBy('start_time')
            ->get();

        $weeklySchedules = $allSchedules->groupBy('day_of_week');

        $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.schedules.show', compact('teacher', 'weeklySchedules', 'daysOrder'));
    }

    public function edit($id): View|RedirectResponse
    {
        // Find the schedule by ID. `findOrFail` will throw a 404 if not found (respecting SchoolScope).
        $schedule = TeacherSchedule::findOrFail($id);

        if (!$this->authorizeTeacherAccess($schedule)) {
            return redirect()->route('schedules.index')->with('error', 'You are not authorized to access this schedule.');
        }

        $school_id = Auth::user()->school_id;
        $teachers = Teacher::whereHas('user', fn($q) => $q->where('school_id', $school_id))->with('user')->get();
        $classes = SchoolClass::where('school_id', $school_id)->get();
        $subjects = Subject::where('school_class_id', $schedule->class_id)->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.schedules.edit', compact('schedule', 'teachers', 'classes', 'subjects', 'days'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // Find the schedule by ID.
        $schedule = TeacherSchedule::findOrFail($id);

        if (!$this->authorizeTeacherAccess($schedule)) {
            return redirect()->route('schedules.index')->with('error', 'You are not authorized to update this schedule.');
        }

        $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required'], // Removed date_format rule
            'end_time' => ['required', 'after:start_time'], // Removed date_format rule
        ]);

        $scheduleData = $request->all();
        $scheduleData['school_id'] = Auth::user()->school_id;

        $schedule->update($scheduleData);

        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        // Find the schedule by ID.
        $schedule = TeacherSchedule::findOrFail($id);

        if (!$this->authorizeTeacherAccess($schedule)) {
            return redirect()->route('schedules.index')->with('error', 'You are not authorized to delete this schedule.');
        }

        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully.');
    }

    public function getSubjectsByClass($class_id): JsonResponse
    {
        $subjects = Subject::where('school_class_id', $class_id)
            ->where('school_id', Auth::user()->school_id)
            ->get(['id', 'name']);
        return response()->json($subjects);
    }

    // Simplified authorization, as SchoolScope handles most of the logic.
    private function authorizeTeacherAccess(TeacherSchedule $schedule): bool
    {
        $user = Auth::user();

        // The SchoolScope already prevents School Admins from accessing other schools' data.
        // We only need this extra check for Teachers to prevent them from accessing other teachers' schedules within the same school.
        if ($user->hasRole('Teacher')) {
            return $user->teacher && $schedule->teacher_id === $user->teacher->id;
        }

        // If the user is not a Teacher (i.e., Super Admin or School Admin), and the record was found, they are authorized.
        return true;
    }
}
