<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultCardController extends Controller
{
    // Constant for passing percentage (e.g., 40%)
    private const PASS_PERCENTAGE = 40;

    /**
     * Display the result card filter page and list of students.
     */
    public function index(Request $request)
    {
        $school_id = Auth::user()->school_id;

        $semesters = Semester::where('school_id', $school_id)->where('status', 'active')->get();
        $classes = SchoolClass::where('school_id', $school_id)->get();

        $selectedSemesterId = $request->input('semester_id');
        $selectedSchoolClassId = $request->input('class_id');

        $students = collect(); // Default to an empty collection
        if ($selectedSemesterId && $selectedSchoolClassId) {
            // If filters are applied, load all students for that class
            $students = Student::where('school_class_id', $selectedSchoolClassId)
                ->with(['user', 'schoolClass']) // Eager load relationships
                ->whereHas('user') // Ensure student has an associated user to prevent errors
                ->get();
        }

        return view('admin.results.index', compact(
            'semesters',
            'classes',
            'students', // Pass the collection of all students
            'selectedSemesterId',
            'selectedSchoolClassId'
        ));
    }

    /**
     * Get result card data for a specific student and semester (AJAX for Modal).
     */
    public function showResultCard(Student $student, Semester $semester)
    {
        // Load necessary relationships
        $student->load('user', 'schoolClass.subjects');

        // Get all subjects for the student's class
        $allClassSubjects = $student->schoolClass->subjects;

        // Get student's marks for the selected semester, keyed by subject_id
        $studentMarks = $student->marks()
            ->where('semester_id', $semester->id)
            ->with('subject') // Eager load subject name
            ->get()
            ->keyBy('subject_id');

        $totalObtained = 0;
        $totalPossible = 0;
        $results = [];
        $isFail = false; // Flag to check if failed in any subject

        foreach ($allClassSubjects as $subject) {
            $mark = $studentMarks->get($subject->id); // Get mark for this subject
            $obtained = $mark->obtained_marks ?? 0;
            $total = $mark->total_marks ?? 100; // Default to 100 if no mark entry

            $totalObtained += $obtained;
            $totalPossible += $total;

            $subjectPercentage = ($total > 0) ? ($obtained / $total) * 100 : 0;
            $subjectStatus = ($subjectPercentage >= self::PASS_PERCENTAGE) ? 'Pass' : 'Fail';

            if ($subjectStatus === 'Fail') {
                $isFail = true; // Mark as failed if any subject is failed
            }

            $results[] = [
                'subject_name' => $subject->name,
                'total_marks' => $total,
                'obtained_marks' => $obtained,
                'status' => $subjectStatus,
            ];
        }

        $overallPercentage = ($totalPossible > 0) ? ($totalObtained / $totalPossible) * 100 : 0;
        // Overall status: Pass only if NOT failed in any subject AND overall percentage is sufficient
        $overallStatus = (!$isFail && $overallPercentage >= self::PASS_PERCENTAGE) ? 'Pass' : 'Fail';

        return response()->json([
            'student_name' => $student->user->name ?? 'N/A',
            'class_name' => $student->schoolClass->name ?? 'N/A',
            'semester_name' => $semester->name,
            'results' => $results,
            'total_obtained' => $totalObtained,
            'total_possible' => $totalPossible,
            'percentage' => round($overallPercentage, 2),
            'status' => $overallStatus,
        ]);
    }
}

