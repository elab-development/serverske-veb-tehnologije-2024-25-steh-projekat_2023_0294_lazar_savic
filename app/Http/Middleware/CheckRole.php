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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->uloga, $roles)) {
            return response()->json([
                'status' => false,
                'poruka' => 'Pristup zabranjen. Nemate odgovarajuću ulogu za ovu akciju.'
            ], 403);
        }

        return $next($request);
    }
}
