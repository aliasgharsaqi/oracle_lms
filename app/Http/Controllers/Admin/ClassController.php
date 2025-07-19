<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::latest()->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('admin.classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_classes'],
        ]);

        SchoolClass::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }
}
