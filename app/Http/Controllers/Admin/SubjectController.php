<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with('schoolClass')->latest()->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $classes = SchoolClass::where('school_id', Auth::user()->school_id)->get();
        return view('admin.subjects.create', compact('classes'));
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'], // Removed 'unique' rule
            'subject_code' => ['nullable', 'string', 'max:50', Rule::unique('subjects')->where('school_id', Auth::user()->school_id)], // Now nullable
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'type' => ['required', Rule::in(['core', 'optional'])],
        ]);

        Subject::create([
            'name' => $request->name,
            'subject_code' => $request->subject_code,
            'school_class_id' => $request->school_class_id,
            'type' => $request->type,
            'created_by' => Auth::id(),
            'school_id' => Auth::user()->school_id,
            'active' => true,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        $classes = SchoolClass::where('school_id', Auth::user()->school_id)->get();
        return view('admin.subjects.edit', compact('subject', 'classes'));
    }


    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'], // Removed 'unique' rule
            'subject_code' => ['nullable', 'string', 'max:50', Rule::unique('subjects')->where('school_id', Auth::user()->school_id)->ignore($subject->id)], // Now nullable
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'type' => ['required', Rule::in(['core', 'optional'])],
        ]);

        $subject->update([
            'name' => $request->name,
            'subject_code' => $request->subject_code,
            'school_class_id' => $request->school_class_id,
            'type' => $request->type,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }
     public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }

    public function toggleStatus(Subject $subject): RedirectResponse
    {
        $subject->active = !$subject->active;
        $subject->save();
        return redirect()->route('subjects.index')->with('success', 'Subject status updated.');
    }
    // ... destroy() and toggleStatus() methods remain the same ...
}