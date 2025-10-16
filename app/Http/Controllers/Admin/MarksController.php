<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarksController extends Controller
{
    /**
     * Display the marks entry page and load relevant data.
     */
    public function index(Request $request)
    {
        $school_id = Auth::user()->school_id;

        // Fetch dropdown data, scoped to the user's school
        $semesters = Semester::where('school_id', $school_id)->where('status', 'active')->get();
        $classes = SchoolClass::with('subjects')->get(); // Already scoped by SchoolScope

        $selectedSemesterId = $request->input('semester_id');
        $selectedClassId    = $request->input('class_id');
        $selectedSubjectId  = $request->input('subject_id');

        $subjects = collect(); // Default to an empty collection
        if ($selectedClassId) {
            $selectedClass = SchoolClass::find($selectedClassId);
            if ($selectedClass) {
                $subjects = $selectedClass->subjects;
            }
        }

        $students = collect();
        if ($selectedClassId && $selectedSubjectId && $selectedSemesterId) {
            // Load students and eagerly load their existing marks for the selected context.
            $students = Student::where('school_class_id', $selectedClassId)
                ->with('user')
                ->with(['marks' => function ($query) use ($selectedSubjectId, $selectedSemesterId) {
                    $query->where('subject_id', $selectedSubjectId)
                          ->where('semester_id', $selectedSemesterId);
                }])
                ->get();
        }

        return view('admin.marks.index', compact(
            'semesters',
            'classes',
            'subjects',
            'students',
            'selectedSemesterId',
            'selectedClassId',
            'selectedSubjectId'
        ));
    }

    /**
     * Store or update a student's marks.
     */
    public function store(Request $request)
    {
        $request->validate([
            'semester_id'    => 'required|exists:semesters,id',
            'subject_id'     => 'required|exists:subjects,id',
            'student_id'     => 'required|exists:students,id',
            'total_marks'    => 'required|integer|min:0|max:1000',
            'obtained_marks' => 'required|integer|min:0|lte:total_marks',
        ]);

        // Use updateOrCreate to prevent duplicate entries.
        Mark::updateOrCreate(
            [
                'student_id'  => $request->student_id,
                'subject_id'  => $request->subject_id,
                'semester_id' => $request->semester_id,
                'school_id'   => Auth::user()->school_id,
            ],
            [
                'total_marks'    => $request->total_marks,
                'obtained_marks' => $request->obtained_marks,
            ]
        );

        // Return a JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Marks saved successfully!']);
        }

        return redirect()->back()->with('success', 'Marks saved successfully!');
    }

    /**
     * Fetch subjects for a given class ID.
     */
    public function getSubjects($class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class) {
            return response()->json([]);
        }
        return response()->json($class->subjects);
    }
}

