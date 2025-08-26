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
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
  public function index(): View
{
    $query = User::where('id', '!=', Auth::id());

    // if (Auth::user()->hasRole('Admin')) {
    //     $query->whereHas('roles', function ($q) {
    //         $q->whereIn('name', ['Staff', 'Student']);
    //     });
    // }

    $users = $query->latest()->get();
    $trashedUsers = User::onlyTrashed()->get();

    return view('admin.users.index', compact('users','trashedUsers'));
}


    public function restore($id)
{
    $user = User::withTrashed()->findOrFail($id);
    $user->restore();
    return redirect()->back()->with('success', 'User restored successfully.');
}
    public function create(): View
{
    $roles = Role::all(); // All available roles
    return view('admin.users.create', compact('roles'));
}


    public function store(Request $request): RedirectResponse
{
    $loggedInUserRole = Auth::user()->getRoleNames()->first();
    $allowedRoles = ($loggedInUserRole === 'Super Admin') 
        ? ['Admin', 'Staff', 'Student'] 
        : ['Staff', 'Student'];

    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
        'role' => ['required', 'string', Rule::in($allowedRoles)],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'status' => 1,
        'created_by' => Auth::id(),
    ]);

    $user->assignRole($request->role);

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
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

 public function update(Request $request, User $user): RedirectResponse
{
    $loggedInUserRole = Auth::user()->getRoleNames()->first();
   

    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'role' => ['required', 'string'],
        'status' => ['required', 'integer'],
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'status' => $request->status,
    ]);

    if ($request->filled('password')) {
        $request->validate(['password' => ['required', 'string', 'min:8']]);
        $user->update(['password' => Hash::make($request->password)]);
    }

    // Sync roles instead of updating "role" column
    $user->syncRoles([$request->role]);

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
