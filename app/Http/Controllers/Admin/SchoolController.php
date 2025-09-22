<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SchoolController extends Controller
{
    /**
     * Display a listing of the schools.
     */
    public function index()
    {
        // Only Super Admin can see all schools. Others will be scoped by the global scope.
        $schools = School::latest()->paginate(10);
        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('admin.schools.create');
    }

    /**
     * Store a new school and its admin user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_email' => 'required|string|email|max:255|unique:schools,email',
            'subscription_plan' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $school = School::create([
                'name' => $validated['school_name'],
                'email' => $validated['school_email'],
                'subscription_plan' => $validated['subscription_plan'],
                'status' => 'active',
            ]);

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('logos', 'public');
                $school->update(['logo' => $path]);
            }

            $schoolAdmin = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'school_id' => $school->id, // Assign the new school's ID
            ]);

            $schoolAdminRole = Role::findByName('School Admin', 'web');
            if ($schoolAdminRole) {
                $schoolAdmin->assignRole($schoolAdminRole);
            }

            DB::commit();

            return redirect()->route('admin.schools.index')->with('success', 'School and Admin created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create school. ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:schools,email,' . $school->id,
            'subscription_plan' => 'required|string',
            'status' => 'required|string|in:active,inactive', // For subscription management
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $school->update($validated);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $school->update(['logo' => $path]);
        }

        return redirect()->route('admin.schools.index')->with('success', 'School updated successfully.');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school)
    {
        // The onDelete('cascade') in the migration will handle deleting related records.
        $school->delete();
        return redirect()->route('admin.schools.index')->with('success', 'School deleted successfully.');
    }
}
