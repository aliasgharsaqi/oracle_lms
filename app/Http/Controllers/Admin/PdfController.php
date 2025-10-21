<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;    // Import your Student model
use App\Models\Semester;  // Import your Semester model
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF; // Import the PDF Facade

class PdfController extends Controller
{
    /**
     * Define the passing percentage.
     * Make sure this matches the constant in your other controller.
     */
    private const PASS_PERCENTAGE = 40; // <-- Adjust if needed

    /**
     * Generates and streams a student's result card as a PDF.
     *
     * @param  \App\Models\Student  $student
     * @param  \App\Models\Semester $semester
     * @return \Illuminate\Http\Response
     */
    public function generateResultCard(Student $student, Semester $semester)
    {
        try {
              $school_id = Auth::user()->school_id;
            $school = School::find($school_id);
            // === START: Logic copied from your showResultCard function ===

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
                
                // Get total marks from the mark entry, fallback to 100
                $total = $mark->total_marks ?? 100; 

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

            // === END: Logic copied from your showResultCard function ===


            // --- Prepare Data for the PDF View ---
            $data = [
                'student'        => $student,
                'semester_name'  => $semester->name,
                'results'        => $results,
                'total_obtained' => $totalObtained,
                'total_possible' => $totalPossible,
                'percentage'     => round($overallPercentage, 2),
                'status'         => $overallStatus,
                'issue_date'     => date('F j, Y'),
                'school'       => $school
            ];

            // Load the view and generate the PDF
            $pdf = PDF::loadView('admin.pdf.result-card', $data);

            // Create a sanitized, dynamic filename
            $filename = Str::slug('Result Card ' . $student->user->name . ' ' . $semester->name) . '.pdf';

            // Stream the PDF to the browser for viewing/printing
            return $pdf->stream($filename);

            // To force a download, uncomment this line:
            // return $pdf->download($filename);

        } catch (\Exception $e) {
            // Log the error and redirect back with a user-friendly message
            \Log::error('PDF Generation Failed for student ' . $student->id . ': ' . $e->getMessage());
            dd($e->getMessage());
            return back()->with('error', 'Could not generate the result card. Please check the data.');
        }
    }

       public function downloadResultCard(Student $student, Semester $semester)
    {
        try {
            $school_id = Auth::user()->school_id;
            $school = School::find($school_id);
            // === START: Logic copied from your showResultCard function ===

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
                
                // Get total marks from the mark entry, fallback to 100
                $total = $mark->total_marks ?? 100; 

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

            // === END: Logic copied from your showResultCard function ===


            // --- Prepare Data for the PDF View ---
            $data = [
                'student'        => $student,
                'semester_name'  => $semester->name,
                'results'        => $results,
                'total_obtained' => $totalObtained,
                'total_possible' => $totalPossible,
                'percentage'     => round($overallPercentage, 2),
                'status'         => $overallStatus,
                'issue_date'     => date('F j, Y'),
                'school'       => $school
            ];

            // Load the view and generate the PDF
            $pdf = PDF::loadView('admin.pdf.result-card', $data);

            // Create a sanitized, dynamic filename
            $filename = Str::slug('Result Card ' . $student->user->name . ' ' . $semester->name) . '.pdf';

            // Stream the PDF to the browser for viewing/printing
            // return $pdf->stream($filename);

            // To force a download, uncomment this line:
            return $pdf->download($filename);

        } catch (\Exception $e) {
            // Log the error and redirect back with a user-friendly message
            \Log::error('PDF Generation Failed for student ' . $student->id . ': ' . $e->getMessage());
            dd($e->getMessage());
            return back()->with('error', 'Could not generate the result card. Please check the data.');
        }
    }
}