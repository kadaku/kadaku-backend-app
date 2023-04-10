<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyMiddleware
{
    public function handle($request, Closure $next)
    {
        $apiKey = env('API_KEY');
        if ($request->header('X-API-KEY') !== $apiKey) {
            // abort(401, 'Unauthorized');
            return response()->json([
                'code' => 401,
                'status' => false,
                'message' => 'Unauthorized, Invalid API key: You must be granted a valid key.',
            ], 401);
        }
        return $next($request);
    }
}