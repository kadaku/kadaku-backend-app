<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyMiddleware
{
    public function handle($request, Closure $next)
    {
        $apiKey = env('API_KEY') . 'kadakuindonesia';
        $copyright = $request->header('X-Copyright');
        $copyright = isset($copyright) && !empty($copyright) ? trim(str_replace(' ', '', strtolower($copyright))) : '';
        if ($request->header('X-Private') . $copyright !== $apiKey) {
            return response()->json([
                'code' => 401,
                'status' => false,
                // 'message' => 'Unauthorized, Invalid API key. You must be granted a valid key.',
                'message' => 'Unauthorized',
            ], 401);
        }
        return $next($request);
    }
}