<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::with('user')->latest()->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'id_card_number' => ['required', 'string', 'max:255', 'unique:teachers'],
            'date_of_birth' => ['required', 'date'],
            'education' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Staff',
                'status' => 1,
                'phone' => $request->phone,
                'user_pic' => $imagePath,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'id_card_number' => $request->id_card_number,
                'date_of_birth' => $request->date_of_birth,
                'education' => $request->education,
                'address' => $request->address,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add teacher. Please try again. Error: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully.');
    }
}
