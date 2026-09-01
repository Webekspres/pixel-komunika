<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPosApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('services.pos.api_token');

        if (! $token || ! hash_equals($token, (string) $request->bearerToken())) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
