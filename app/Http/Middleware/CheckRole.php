<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Owner/Superadmin always has full bypass
        if ($user->isOwner()) {
            return $next($request);
        }

        if (! empty($roles) && ! $user->hasRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Akses ditolak: Role Anda ({$user->role_label}) tidak memiliki hak akses untuk resource ini.",
                ], 403);
            }

            return redirect($user->default_route)
                ->with('warning', "Akses Dibatasi: Role Anda ({$user->role_label}) tidak memiliki hak akses ke modul tersebut.");
        }

        return $next($request);
    }
}
