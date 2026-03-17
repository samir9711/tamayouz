<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMinAdminOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();
        if ($admin) {
            return $next($request);
        }

        // Ministry guard
        $minAcc = auth('ministry')->user();
        if (!$minAcc) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        // التحقق ببساطة من الحقل role
        if (!isset($minAcc->role) || (string)$minAcc->role !== 'min_admin') {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden. min_admin role required.');
        }

        return $next($request);


    }
}
