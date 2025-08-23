<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Student::class);
        $students = Student::with(['user', 'schoolClass'])->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        $this->authorize('create', Student::class);
        $classes = SchoolClass::all();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Student::class);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable','string', 'email', 'max:255'],
            'password' => ['nullable','string', 'min:8'],
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'father_name' => ['required', 'string', 'max:255'],
            'id_card_number' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'father_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section' => ['nullable','string', 'max:255'],
            'previous_docs' => ['nullable', 'file', 'max:5120'],
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Student',
                'status' => 1,
                'phone' => $request->phone,
                'user_pic' => $imagePath,
            ]);

            $docsPath = $request->hasFile('previous_docs') ? $request->file('previous_docs')->store('previous_documents', 'public') : null;

            Student::create([
                'user_id' => $user->id,
                'father_name' => $request->father_name,
                'id_card_number' => $request->id_card_number,
                'father_phone' => $request->father_phone,
                'address' => $request->address,
                'school_class_id' => $request->school_class_id,
                'section' => $request->section,
                'previous_school_docs' => $docsPath,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to enroll student. Please try again. Error: ' . $e->getMessage());
        }

        return redirect()->route('students.index')->with('success', 'Student enrolled successfully.');
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);
        $student->load(['user', 'schoolClass']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $this->authorize('update', $student);
        $student->load('user');
        $classes = SchoolClass::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);
        $user = $student->user;
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable','string', 'email', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'father_name' => ['required', 'string', 'max:255'],
            'id_card_number' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'father_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section' => ['nullable','string', 'max:255'],
            'previous_docs' => ['nullable', 'file', 'max:5120'],
        ]);


        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            if ($request->hasFile('profile_image')) {
                Storage::disk('public')->delete($user->user_pic);
                $user->user_pic = $request->file('profile_image')->store('profile_images', 'public');
                $user->save();
            }
            
            $docsPath = $student->previous_school_docs;
            if ($request->hasFile('previous_docs')) {
                Storage::disk('public')->delete($student->previous_school_docs);
                $docsPath = $request->file('previous_docs')->store('previous_documents', 'public');
            }

            $student->update([
                'father_name' => $request->father_name,
                'id_card_number' => $request->id_card_number,
                'father_phone' => $request->father_phone,
                'address' => $request->address,
                'school_class_id' => $request->school_class_id,
                'section' => $request->section,
                'previous_school_docs' => $docsPath,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update student. Please try again. Error: ' . $e->getMessage());
        }

        return redirect()->route('students.index')->with('success', 'Student record updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);
        
        DB::beginTransaction();
        try {
               $userPic = $student->user->user_pic ?? null;
        $previousDocs = $student->previous_school_docs ?? null;
            // The user record will be deleted automatically due to the cascade constraint
            $student->delete();
             if ($userPic) {
            Storage::disk('public')->delete($userPic);
        }
        if ($previousDocs) {
            Storage::disk('public')->delete($previousDocs);
        }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete student. Please try again.');
        }

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
