<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::where('id', '!=', Auth::id());

        if (Auth::user()->role === 'Admin') {
            $query->whereIn('role', ['Staff', 'Student']);
        }

        $users = $query->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $loggedInUserRole = Auth::user()->role;
        $roles = [];

        if ($loggedInUserRole === 'Super Admin') {
            $roles = ['Admin', 'Staff', 'Student'];
        } elseif ($loggedInUserRole === 'Admin') {
            $roles = ['Staff', 'Student'];
        }

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $loggedInUserRole = Auth::user()->role;
        $allowedRoles = ($loggedInUserRole === 'Super Admin') ? ['Admin', 'Staff', 'Student'] : ['Staff', 'Student'];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 1,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $loggedInUserRole = Auth::user()->role;
        $roles = [];

        if ($loggedInUserRole === 'Super Admin') {
            $roles = ['Admin', 'Staff', 'Student'];
        } elseif ($loggedInUserRole === 'Admin') {
            $roles = ['Staff', 'Student'];
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $loggedInUserRole = Auth::user()->role;
        $allowedRoles = ($loggedInUserRole === 'Super Admin') ? ['Admin', 'Staff', 'Student'] : ['Staff', 'Student'];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'status' => ['required', 'integer'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', 'string', 'min:8']]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);
        
        // Prevent user from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
