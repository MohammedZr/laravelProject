<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
// app/Http/Middleware/EnsureRole.php
public function handle($request, \Closure $next, ...$roles)
{
    if (!auth()->check() || !in_array(auth()->user()->role, $roles, true)) {
        abort(403);
    }
    return $next($request);
}

}
    