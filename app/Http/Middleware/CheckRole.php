<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Role hierarchy array (higher index = higher permissions)
     * This establishes that superadmin has the highest access level.
     */
    protected $roleHierarchy = [
        'user' => 1,
        'nurse' => 2,
        'doctor' => 3,
        'admin' => 4,
        'superadmin' => 5, // Highest level
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        
        // Log for debugging
        Log::info('Role Check', [
            'required_role' => $role,
            'user_role' => $userRole,
            'user_id' => $request->user()->id,
            'user_name' => $request->user()->name
        ]);
        
        // If the user is a superadmin, they automatically have access to everything
        if ($userRole === 'superadmin') {
            return $next($request);
        }
        
        // For other roles, check if they have the required role or higher in the hierarchy
        $requiredLevel = $this->roleHierarchy[$role] ?? 0;
        $userLevel = $this->roleHierarchy[$userRole] ?? 0;
        
        if ($userLevel >= $requiredLevel) {
            return $next($request);
        }
        
        // If role check fails, redirect to dashboard with an error message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
}
