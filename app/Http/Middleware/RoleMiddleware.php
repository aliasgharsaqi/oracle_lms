<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Yeh import karein
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Pehle check karein ke user logged in hai
        if (!Auth::check()) {
            return redirect('login');
        }

        // Check karein ke user ka role match karta hai
        // Yeh assume kar raha hai ke 'role' column hai User model par
        if ($request->user()->role != $role) {
            
            // Agar role match nahi hota (e.g., 'teacher' 'student' ka page khol raha hai)
            // To usay 403 (Forbidden) error dikhayein.
            abort(403, 'Unauthorized Action.');
        }
        
        // Agar role match ho jaye to request ko aage jaane dein
        return $next($request);
    }
}