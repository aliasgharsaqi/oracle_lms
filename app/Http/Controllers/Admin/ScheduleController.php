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
        ]);

        TeacherSchedule::create($request->all());

        return redirect()->route('schedules.index')->with('success', 'Lecture scheduled successfully.');
    }
}
