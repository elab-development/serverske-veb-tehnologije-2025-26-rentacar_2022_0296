<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Ako korisnik nije ulogovan ili njegova uloga nije u dozvoljenim ulogama
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Pristup zabranjen. Nemate odgovarajuća prava pristupa.'
            ], 403);
        }

        return $next($request);
    }
}
