<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsItem; // Keep LmsItem import (if used elsewhere)
use App\Models\Quiz;
use App\Models\SchoolClass; // Correct model for class data
use Illuminate\Http\Request;

class QuizController extends Controller 
{
    public function index()
    {
        // 1. Fetch all quizzes for initial grid render
        $quizzes = Quiz::latest()->get(); 
        
        // 2. CORRECT CLASS FETCHING: Use SchoolClass model to fetch classes
        // The data source for the filter dropdowns is SchoolClass.
        $classes = SchoolClass::select('id', 'name')->get(); 
        
        // 3. Pass BOTH variables to the view
        return view('admin.quiz', compact('quizzes', 'classes')); 
    }

    public function getAll(Request $request)
    {
        $query = Quiz::latest();

    if ($classId = $request->query('class_id')) {
        // This MUST be filtering by the class_id column.
        $query->where('class_id', $classId); 
    }

    $quizzes = $query->get();
        
        $total = $quizzes->count();
        $published = $quizzes->where('status', 'Published')->count();
        $drafts = $quizzes->where('status', 'Draft')->count();

        return response()->json([
            'quizzes' => $quizzes,
            'stats' => [
                'total' => $total,
                'published' => $published,
                'drafts' => $drafts,
            ]
        ]);
    }

    // Handles Create/Update
public function storeOrUpdate(Request $request)
    {
        // 1. Validation (Using the correct school_classes reference)
        $validated = $request->validate([
            'class_id' => 'required|integer|exists:school_classes,id', 
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'status' => 'required|in:Published,Draft',
            'questions' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
            'dueDate' => 'required|date',
            'quizId' => 'nullable|integer|exists:quizzes,id',
        ]);

        $quiz = $request->quizId 
            ? Quiz::findOrFail($request->quizId)
            : new Quiz();

        // 2. CRITICAL FIX: Set school_id based on the currently authenticated user
        // This prevents the SchoolScope from crashing the save operation.
        $schoolId = Auth::user()->school_id ?? null; // Adjust path if needed (e.g., Auth::id() if user model has school_id)
        
        if (!$schoolId) {
            // Handle case where user is logged in but has no school_id
            return response()->json(['message' => 'Error: User is not associated with a school. Cannot save quiz.'], 403);
        }
        
        $quiz->fill([
            'class_id' => $validated['class_id'], 
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'status' => $validated['status'],
            'questions' => $validated['questions'],
            'duration' => $validated['duration'],
            'due_date' => $validated['dueDate'],
            'school_id' => $schoolId, // Ensure this is set!
        ])->save();

        return response()->json($quiz, $request->quizId ? 200 : 201);
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return response()->json(null, 204);
    }
}