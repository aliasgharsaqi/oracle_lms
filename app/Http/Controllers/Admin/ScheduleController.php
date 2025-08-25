<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = TeacherSchedule::with(['teacher.user', 'schoolClass', 'subject'])->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        $teachers = Teacher::with('user')->get();
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('admin.schedules.create', compact('teachers', 'classes', 'subjects', 'days'));
    }

    public function store(Request $request): RedirectResponse
    {
         $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ],
          [
            'teacher_id.required' => 'Select Teacher Name',
            'class_id.required' => 'Select Class Name',
            'subject_id.required' => 'Select Subject Name',
            'day_of_week.required' => 'Select Days',
             'start_time.required' => 'Select Start Time',
              'end_time.required' => 'Select End Time',
        ]);

        TeacherSchedule::create($request->all());

        return redirect()->route('schedules.index')->with('success', 'Lecture scheduled successfully.');
    }

    public function show($id): View
    {
        $schedule = TeacherSchedule::with(['teacher.user', 'schoolClass', 'subject'])->findOrFail($id);
        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit($id): View
    {
        $schedule = TeacherSchedule::findOrFail($id);
        $teachers = Teacher::with('user')->get();
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('admin.schedules.edit', compact('schedule', 'teachers', 'classes', 'subjects', 'days'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
        ]);

        $schedule = TeacherSchedule::findOrFail($id);
        $schedule->update($request->all());

        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $schedule = TeacherSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully.');
    }
}
