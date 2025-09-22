<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSchoolStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // We only perform this check for authenticated users who are NOT Super Admins.
        if ($user && !$user->hasRole('Super Admin')) {
            
            // If the user has no school assigned or the school's status is not 'active',
            // log them out and redirect them.
            if (!$user->school || $user->school->status !== 'active') {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('error', 'Your school account is currently inactive. Please contact support.');
            }
        }

        return $next($request);
    }
}
