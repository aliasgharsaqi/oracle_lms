<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::latest()->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects'],
            'subject_code' => ['required', 'string', 'max:50', 'unique:subjects'],
        ]);

        Subject::create([
            'name' => $request->name,
            'subject_code' => $request->subject_code,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }
}
