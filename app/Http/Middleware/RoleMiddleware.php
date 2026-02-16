<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param array<int, string> $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return Route::has('login')
                ? redirect()->route('login')
                : abort(401, 'You must be logged in.');
        }

        $roles = $roles ?: [User::ROLE_ADMIN];

        if (!$user->hasRole($roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
