<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect(route('login'));
        }

        $user = $request->user();

        // If no specifically requested roles, default to allowing admin and employee
        if (empty($roles)) {
            $roles = ['admin', 'employee', 'super_admin'];
        }

        $userRole = $user->role;
        $nodeRole = $user->roleNode ? $user->roleNode->name : null;

        if (in_array($userRole, $roles) || in_array($nodeRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Access denied. Unauthorized role tier.');
    }
}
