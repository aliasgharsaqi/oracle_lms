<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MarksExport;
use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        $selectedSchoolClassId  = $request->input('class_id'); // Renamed for clarity
        $selectedSubjectId  = $request->input('subject_id');

        $subjects = collect(); // Default to an empty collection
        if ($selectedSchoolClassId) {
            $selectedClass = SchoolClass::find($selectedSchoolClassId);
            if ($selectedClass) {
                $subjects = $selectedClass->subjects;
            }
        }

        $students = collect();
        if ($selectedSchoolClassId && $selectedSubjectId && $selectedSemesterId) {
            // Load students and eagerly load their existing marks for the selected context.
            $students = Student::where('school_class_id', $selectedSchoolClassId)
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
            'selectedSchoolClassId', // Updated variable name
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
            'class_id'       => 'required|exists:school_classes,id',
            'subject_id'     => 'required|exists:subjects,id',
            'student_id'     => 'required|exists:students,id',
            'total_marks'    => 'required|integer|min:0|max:1000',
            'obtained_marks' => 'required|integer|min:0|lte:total_marks',
        ]);

        $schoolClass = SchoolClass::find($request->class_id);
        if (!$schoolClass) {
            return response()->json(['error' => true, 'message' => 'Invalid class selected.']);
        }

       $result =  Mark::updateOrCreate(
            [
                'student_id'      => $request->student_id,
                'subject_id'      => $request->subject_id,
                'semester_id'     => $request->semester_id,
                'school_id'       => Auth::user()->school_id,
                'school_class_id' => $schoolClass->id,
            ],
            [
                'total_marks'     => $request->total_marks,
                'obtained_marks'  => $request->obtained_marks,
            ]
        );

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Marks saved successfully!']);
        } else {
            return response()->json(['error' => true, 'message' => 'Marks not saved successfully!']);
        }
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

    /**
     * Export the filtered marks data to an Excel file.
     */
    public function export(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'class_id'    => 'required|exists:school_classes,id',
            'subject_id'  => 'required|exists:subjects,id',
        ]);

        $selectedSchoolClassId = $request->input('class_id');
        $selectedSubjectId = $request->input('subject_id');
        $selectedSemesterId = $request->input('semester_id');

        $students = Student::where('school_class_id', $selectedSchoolClassId)
            ->with('user')
            ->with(['marks' => function ($query) use ($selectedSubjectId, $selectedSemesterId) {
                $query->where('subject_id', $selectedSubjectId)
                      ->where('semester_id', $selectedSemesterId);
            }])
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'No data available to export for the selected criteria.');
        }

        $class = SchoolClass::find($selectedSchoolClassId);
        $subject = Subject::find($selectedSubjectId);
        $fileName = "Marks-{$class->name}-{$subject->name}.xlsx";

        return Excel::download(new MarksExport($students, $selectedSubjectId, $selectedSemesterId), $fileName);
    }
}

