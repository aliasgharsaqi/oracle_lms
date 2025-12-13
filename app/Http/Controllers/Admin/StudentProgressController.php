<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentProgressController extends Controller
{
    /**
     * Shows the main view and passes all classes for initial load.
     */
    public function index(): View
    {
        $classes = SchoolClass::select('id', 'name')->get(); 
        
        return view('admin.diary.student_diary', compact('classes')); 
    }

    /**
     * AJAX Endpoint 1: Fetches subjects related to a specific class.
     */
    public function getSubjectsByClass($class_id): JsonResponse
    {
        $subjects = Subject::where('school_class_id', $class_id)
                           ->select('id', 'name')
                           ->get();

        return response()->json($subjects);
    }

    /**
     * AJAX Endpoint 2: Fetches student list and their progress for the selected filters.
     */
 public function getStudentProgressData(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'subject_name' => 'nullable|string|max:255', 
            'date' => 'required|date',
        ]);

        $classId = $request->class_id;
        $subjectName = $request->subject_name; 
        $date = $request->date;

        // --- FETCH ALL SUBJECTS FOR THE CLASS (MANDATORY FOR 'ALL SUBJECTS' VIEW) ---
        // This will be used to show subjects that have NO progress entries.
        $allClassSubjects = Subject::where('school_class_id', $classId)
                                   ->select('name')
                                   ->pluck('name')
                                   ->toArray();
        
        $students = Student::where('school_class_id', $classId)
                           ->select('id', 'user_id', 'school_class_id') 
                           ->with(['user' => function ($query) {
                               $query->select('id', 'user_pic', 'name'); 
                           }])
                           ->with(['diaryEntries' => function ($query) use ($subjectName, $date) {
                               $query->whereDate('date', $date)
                                     // Filter by subject ONLY IF subjectName is provided
                                     ->when($subjectName, function ($q) use ($subjectName) {
                                         return $q->where('subject', $subjectName);
                                     })
                                     ->select('student_id', 'subject', 'homework', 'teacher_notes', 'status'); 
                           }])
                           ->orderBy('id')
                           ->get();

        $className = SchoolClass::find($classId)->name ?? 'Unknown Class';
        
        $progressData = $students->map(function ($student) use ($subjectName) {
            
            $studentName = $student->user->name ?? 'N/A';
            
            if ($subjectName) {
                // SINGLE SUBJECT VIEW: Returns one simple 'entry' object
                $entry = $student->diaryEntries->first();
                return [
                    'id' => $student->id,
                    'name' => $studentName, 
                    'avatarUrl' => $student->user->user_pic ? asset('storage/profile_pics/' . $student->user->user_pic) : asset('images/student.jpeg'),
                    'entry' => $entry ? [
                        'homework' => $entry->homework,
                        'teacherNotes' => $entry->teacher_notes,
                        'status' => $entry->status,
                    ] : null,
                ];
            } 
            
            // ALL SUBJECTS VIEW: Returns an array of existing entries, keyed by subject name
            $allEntries = $student->diaryEntries->keyBy('subject')->map(function ($entry) {
                return [
                    'homework' => $entry->homework,
                    'teacherNotes' => $entry->teacher_notes,
                    'status' => $entry->status,
                ];
            })->toArray();

            return [
                'id' => $student->id,
                'name' => $studentName, 
                'avatarUrl' => $student->user->user_pic ? asset('storage/profile_pics/' . $student->user->user_pic) : asset('images/student.jpeg'),
                'allEntries' => $allEntries, // Existing entries only
            ];
        });

        return response()->json([
            'class_name' => $className,
            'subject_name' => $subjectName, 
            'all_class_subjects' => $allClassSubjects, // NEW: Full list of subjects for the class
            'students_progress' => $progressData,
        ]);
    }

    /**
     * AJAX Endpoint 3: Handles saving or updating the progress data.
     */
    public function saveProgress(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string',
            'date' => 'required|date',
            'homework' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            $student = Student::findOrFail($request->student_id);

            // Find or create the diary entry (the unique constraint is student_id, subject, date)
            $diaryEntry = $student->diaryEntries()->firstOrNew([
                'subject' => $request->subject,
                'date' => $request->date,
                // Include school_id in the attributes for creation
                'school_id' => $student->school_id 
            ]);

            $diaryEntry->homework = $request->homework;
            $diaryEntry->teacher_notes = $request->notes;
            $diaryEntry->status = 'Completed'; 
            $diaryEntry->school_id = $student->school_id; // Ensure school_id is set

            $diaryEntry->save();
            DB::commit();

            return response()->json(['message' => 'Progress saved successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Progress Save Failed: " . $e->getMessage(), $request->all());
            return response()->json(['message' => 'Failed to save progress. Database error.'], 500);
        }
    }
}