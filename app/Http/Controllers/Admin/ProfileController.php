<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = \App\Models\User::find(Auth::id());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'twitter_profile' => ['nullable', 'string', 'max:255'],
            'facebook_profile' => ['nullable', 'string', 'max:255'],
            'linkedin_profile' => ['nullable', 'string', 'max:255'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->twitter_profile = $validated['twitter_profile'] ?? null;
        $user->facebook_profile = $validated['facebook_profile'] ?? null;
        $user->linkedin_profile = $validated['linkedin_profile'] ?? null;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    public function updateProfileImage(Request $request): RedirectResponse
    {
        $request->validate([
            'user_pic' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = \App\Models\User::find(Auth::id());

        if ($request->hasFile('user_pic')) {
            $path = $request->file('user_pic')->store('profile_images', 'public');
            
            // Optional: Delete old image
            // if($user->user_pic) { Storage::disk('public')->delete($user->user_pic); }

            $user->user_pic = $path;
            $user->save();
        }

        return redirect()->route('profile.edit')->with('status', 'image-updated');
    }
}
