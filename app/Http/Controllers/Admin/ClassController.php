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

    public function edit($id): View
    {
        $data = SchoolClass::find($id);
        return view('admin.classes.edit', compact('data'));
    }

    public function update(request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data = SchoolClass::find($id);
        $data->name = $request->name;
        $data->created_by = Auth::id();
        $data->save();

        return redirect()->route('classes.index')->with('success', 'Class update successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_classes'],
        ]);

        SchoolClass::create([
            'name' => $request->name,
            'school_id' => auth()->user()->school_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }
}
