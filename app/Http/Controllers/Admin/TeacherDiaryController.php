<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TeacherDiaryController extends Controller
{
    /**
     * Display the initial Teacher Diary page.
     */
    public function index(): View
    {
        $school_id = Auth::user()->school_id;
        
        // Fetch all teachers for the dropdown
        $teachers = Teacher::with('user')
            ->where('school_id', $school_id)
            ->get();
        
        // Fetch all classes for the assignment modal
        $classes = SchoolClass::where('school_id', $school_id)->get();
        
        // Passing an empty collection for subjects as they are loaded via AJAX now
        $subjects = collect(); 

        return view('admin.diary.teacher_diary', compact('teachers', 'classes', 'subjects'));
    }

    /**
     * Fetch the subjects belonging to the selected classes (for the assignment modal).
     * Also checks for existing assignments on the selected date to disable them.
     */
    public function getSubjectsByClasses(Request $request): JsonResponse
    {
        $request->validate([
            'class_ids' => 'required|string', // Comma-separated string of IDs
            'due_date' => 'required|date',
            'teacher_id' => 'required|exists:teachers,id',
        ]);
        
        $school_id = Auth::user()->school_id;
        $classIds = explode(',', $request->class_ids);
        $dueDate = $request->due_date;
        $teacherId = $request->teacher_id;

        // Security check: Ensure the selected teacher belongs to the current school
        $teacher = Teacher::find($teacherId);
        if (!$teacher || $teacher->school_id !== $school_id) {
            return response()->json(['error' => 'Unauthorized teacher ID'], 403);
        }

        // 1. Fetch relevant subjects (where subject's school_class_id is in the selected IDs)
        $subjects = Subject::where('school_id', $school_id)
                           ->whereIn('school_class_id', $classIds)
                           ->where('active', 1) 
                           ->orderBy('name')
                           ->get(['id', 'name']);

        // 2. Check for existing assignments for the selected teacher/date/classes
        // We look for existing assignments that match the teacher, the due date, AND are tied to one of the selected classes.
        $assignedSubjects = TeacherAssignment::where('school_id', $school_id)
            ->where('teacher_id', $teacherId)
            ->where('due_date', $dueDate)
            ->whereIn('class_id', $classIds) 
            ->pluck('subject_id')
            ->unique()
            ->toArray();

        return response()->json([
            'subjects' => $subjects,
            'assigned_subjects' => $assignedSubjects,
        ]);
    }

    /**
     * Fetch a specific teacher's full records (assignments) for dynamic display.
     * It now accepts a filter_date parameter to show only assignments for that date.
     */
    public function getTeacherRecord(Teacher $teacher, Request $request): JsonResponse
    {
        // Security check: Ensure the teacher belongs to the current user's school
        if ($teacher->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Get the filter date from the request, defaulting to today if not provided.
        $filterDate = $request->query('filter_date', now()->format('Y-m-d'));

        // Fetch assignments, filtering by the exact due_date provided in the request
        $assignmentsQuery = $teacher->assignments()
            ->with(['schoolClass', 'subject'])
            ->where('due_date', $filterDate); // <-- FILTER BY DATE

        $assignments = $assignmentsQuery->orderBy('due_date', 'asc')->get();

        // Calculate progress stats (Note: these stats now reflect only the filtered assignments)
        $total = $assignments->count();
        $completed = $assignments->where('status', 'completed')->count();
        $verified = $assignments->where('status', 'verified')->count();
        $pending = $total - $completed - $verified;

        // Group the assignments by class/subject for the view 
        $groupedAssignments = $assignments->groupBy(function($item) {
            return ($item->schoolClass->name ?? 'N/A Class') . ' - ' . ($item->subject->name ?? 'N/A Subject');
        })->map(function ($group) {
            return $group->sortBy('due_date');
        });

        return response()->json([
            'teacher' => [
                'name' => $teacher->user->name,
                // Check if user_pic exists before building asset URL
                'avatar_url' => $teacher->user->user_pic ? asset('storage/' . $teacher->user->user_pic) : null,
            ],
            'assignments' => $groupedAssignments,
            'stats' => [
                'total' => $total,
                'completed' => $completed,
                'verified' => $verified,
                'pending' => $pending,
            ]
        ]);
    }

    /**
     * Store a new task assignment.
     */
    public function storeTask(Request $request): JsonResponse
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:school_classes,id',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'homework_assignment' => 'required|string|max:1000',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $school_id = Auth::user()->school_id;
        $teacher = Teacher::findOrFail($request->teacher_id);

        if ($teacher->school_id !== $school_id) {
            return response()->json(['error' => 'Unauthorized teacher ID'], 403);
        }

        $assignmentsCount = 0;
        foreach ($request->class_ids as $classId) {
            foreach ($request->subject_ids as $subjectId) {
                // Ensure the subject actually belongs to the class *and* is part of the current school
                $isSubjectValid = Subject::where('id', $subjectId)
                                         ->where('school_class_id', $classId)
                                         ->where('school_id', $school_id)
                                         ->exists();
                
                if ($isSubjectValid) {
                     // Use updateOrCreate to avoid duplicate entries on the same due date/teacher/class/subject
                     $assignment = TeacherAssignment::updateOrCreate(
                        [
                            'teacher_id' => $teacher->id,
                            'class_id' => $classId,
                            'subject_id' => $subjectId,
                            'due_date' => $request->due_date,
                        ],
                        [
                            'school_id' => $school_id,
                            'homework_assignment' => $request->homework_assignment,
                            'status' => 'pending', // Always start as pending
                        ]
                    );
                    $assignmentsCount++;
                }
            }
        }

        if ($assignmentsCount === 0) {
            return response()->json(['error' => 'No valid class-subject combinations found for assignment. Please check subject-class mapping.'], 422);
        }

        return response()->json([
            'success' => true, 
            'message' => $assignmentsCount . ' tasks assigned successfully.',
            'count' => $assignmentsCount
        ]);
    }
    
    /**
     * Update the progress/status of a specific assignment.
     */
    public function updateProgress(Request $request, TeacherAssignment $assignment): JsonResponse
    {
        // Security check
        if ($assignment->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,completed,verified',
            'teacher_notes' => 'nullable|string|max:500',
        ]);
        
        $assignment->update($request->only('status', 'teacher_notes'));

        return response()->json(['success' => true, 'message' => 'Progress updated.']);
    }

    /**
     * Show the monthly report view.
     */
    public function monthlyReport(Request $request): View
    {
        $school_id = Auth::user()->school_id;
        $selectedTeacherId = $request->input('teacher_id');
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        $teacher = null;
        $assignments = collect();

        if ($selectedTeacherId) {
            $teacher = Teacher::with('user')->where('id', $selectedTeacherId)->where('school_id', $school_id)->firstOrFail();
            
            $assignments = $teacher->assignments()
                ->with(['schoolClass', 'subject'])
                ->whereYear('due_date', Carbon::parse($selectedMonth)->year)
                ->whereMonth('due_date', Carbon::parse($selectedMonth)->month)
                ->orderBy('due_date', 'asc')
                ->get();
        }
        
        $teachers = Teacher::with('user')->where('school_id', $school_id)->get();
        $monthName = Carbon::parse($selectedMonth)->format('F Y');

        return view('admin.diary.monthly_report', compact('teachers', 'teacher', 'assignments', 'selectedMonth', 'monthName', 'selectedTeacherId'));
    }
}