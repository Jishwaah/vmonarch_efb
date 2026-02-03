<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscordRoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $authorized = match ($role) {
            'pilot' => $user->isPilot(),
            'staff' => $user->isStaff(),
            default => false,
        };

        if (! $authorized) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
