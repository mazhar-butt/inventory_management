<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect('/login');
        }

        // Check if user has role
        if (!$user->role || $user->role->slug !== $role) {
            // Render the unauthorized page for AJAX/Inertia requests
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            return inertia('Unauthorized');
        }

        return $next($request);
    }
}
