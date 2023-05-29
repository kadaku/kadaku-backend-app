<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $services = ['facebook', 'twitter', 'linkedin', 'google', 'github', 'gitlab', 'bitbucket'];
        $enabledServices = [];
        foreach ($services as $service) {
            if (config('services' . $service)) {
                $enabledServices[] = $service;
            }
        }

        dd($enabledServices);

        if (!in_array(strtolower($request->service), $enabledServices)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 403,
                    'status' => false,
                    'message' => 'Invalid social service',
                ], 403);
            } else {
                return redirect()->back();
            }
        }
        return $next($request);
    }
}
