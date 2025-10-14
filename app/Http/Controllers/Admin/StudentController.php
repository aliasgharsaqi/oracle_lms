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
use Illuminate\Validation\Rules as PasswordRules;

class StudentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Student::class);
        $students = Student::with('user', 'schoolClass')->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        $this->authorize('create', Student::class);
        // --- THIS IS THE FIX ---
        // Be explicit instead of relying only on the global scope.
        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Student::class);

        $schoolId = auth()->user()->school_id;

        if (!$schoolId) {
            return back()->with('error', 'No school assigned to your account. Please contact administrator.');
        }

        // Validate input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['nullable', 'string', 'min:8'],
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'father_name' => ['required', 'string', 'max:255'],
            'id_card_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students')->where(fn($q) => $q->where('school_id', $schoolId))
            ],
            'phone' => ['required', 'string', 'max:20'],
            'father_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'school_class_id' => [
                'required',
                Rule::exists('school_classes', 'id')->where(fn($q) => $q->where('school_id', $schoolId))
            ],
            'section' => ['nullable', 'string', 'max:255'],
            'previous_docs' => ['nullable', 'file', 'max:5120'],
        ]);

        DB::beginTransaction();
        try {
            // confirm class belongs to this school
            $class = SchoolClass::where('id', $request->school_class_id)
                ->where('school_id', $schoolId)
                ->first();

            if (!$class) {
                throw new \Exception('Selected class does not belong to your school.');
            }

            $imagePath = $request->file('profile_image')->store('profile_images', 'public');

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password'),
                'school_id' => $schoolId,
                'status' => 1,
                'phone' => $request->phone,
                'user_pic' => $imagePath,
            ]);

            $user->assignRole('Student');

            $docsPath = $request->hasFile('previous_docs')
                ? $request->file('previous_docs')->store('previous_documents', 'public')
                : null;

            Student::create([
                'user_id' => $user->id,
                'father_name' => $request->father_name,
                'id_card_number' => $request->id_card_number,
                'father_phone' => $request->father_phone,
                'address' => $request->address,
                'school_class_id' => $request->school_class_id,
                'section' => $request->section,
                'previous_school_docs' => $docsPath,
                'school_id' => $schoolId,
            ]);

            DB::commit();
            return redirect()->route('students.index')->with('success', 'Student enrolled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to enroll student. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }


    public function show(Student $student): View
    {
        $this->authorize('view', $student);
        $student->load(['user.school', 'schoolClass']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $this->authorize('update', $student);
        $student->load('user');
        // --- THIS IS THE FIX ---
        $classes = SchoolClass::where('school_id', auth()->user()->school_id)->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $schoolId = auth()->user()->school_id;
        if (!$schoolId) {
            return back()->with('error', 'No school assigned to your account. Please contact administrator.');
        }

        $user = $student->user;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'father_name' => ['required', 'string', 'max:255'],
            'id_card_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students')
                    ->where(fn($q) => $q->where('school_id', $schoolId))
                    ->ignore($student->id)
            ],
            'phone' => ['required', 'string', 'max:20'],
            'father_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'school_class_id' => [
                'required',
                Rule::exists('school_classes', 'id')->where(fn($q) => $q->where('school_id', $schoolId))
            ],
            'section' => ['nullable', 'string', 'max:255'],
            'previous_docs' => ['nullable', 'file', 'max:5120'],
        ]);

        DB::beginTransaction();
        try {
            // confirm class belongs to this school
            $class = SchoolClass::where('id', $request->school_class_id)
                ->where('school_id', $schoolId)
                ->first();

            if (!$class) {
                throw new \Exception('Selected class does not belong to your school.');
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            if ($request->hasFile('profile_image')) {
                if ($user->user_pic) {
                    Storage::disk('public')->delete($user->user_pic);
                }
                $user->user_pic = $request->file('profile_image')->store('profile_images', 'public');
                $user->save();
            }

            $docsPath = $student->previous_school_docs;
            if ($request->hasFile('previous_docs')) {
                if ($student->previous_school_docs) {
                    Storage::disk('public')->delete($student->previous_school_docs);
                }
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
            return redirect()->route('students.index')->with('success', 'Student record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update student. Error: ' . $e->getMessage())->withInput();
        }
    }


    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);
        DB::beginTransaction();
        try {
            $user = $student->user;
            $student->delete();
            if ($user) $user->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to move student to trash.');
        }
        return redirect()->route('students.index')->with('success', 'Student moved to trash successfully.');
    }
}
