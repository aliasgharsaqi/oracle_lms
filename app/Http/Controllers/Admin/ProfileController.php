<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     */
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information, password, social links, and profile image
     * through a single, consolidated method.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $section = $request->input('section');
        $status_key = 'status';
        $status_message = 'profile-updated';

        switch ($section) {
            case 'info':
                $validated = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                ]);
                $user->update($validated);
                $status_message = 'profile-info-updated';
                break;

            case 'social':
                $validated = $request->validate([
                    'twitter_profile' => ['nullable', 'url', 'max:255'],
                    'facebook_profile' => ['nullable', 'url', 'max:255'],
                    'linkedin_profile' => ['nullable', 'url', 'max:255'],
                ]);
                $user->update($validated);
                $status_message = 'social-links-updated';
                break;

            case 'password':
                $validated = $request->validate([
                    'current_password' => ['required', 'current_password'],
                    'password' => ['required', Password::defaults(), 'confirmed'],
                ]);
                $user->update(['password' => Hash::make($validated['password'])]);
                $status_message = 'password-updated';
                break;

            case 'image':
                $request->validate(['user_pic' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);
                if ($request->hasFile('user_pic')) {
                    // Delete the old image if it exists to save space
                    if ($user->user_pic) {
                        Storage::disk('public')->delete($user->user_pic);
                    }
                    $path = $request->file('user_pic')->store('profile_images', 'public');
                    $user->update(['user_pic' => $path]);
                    $status_message = 'image-updated';
                }
                break;
            
            default:
                 return redirect()->route('profile.edit')->with('error', 'Invalid update section specified.');
        }

        return redirect()->route('profile.edit')->with($status_key, $status_message);
    }
}

