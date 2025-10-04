<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class VerifyJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'Unauthorized — no token provided'], 401);
        }

        // call auth-service /me
        $authUrl = rtrim(env('AUTH_SERVICE_URL'), '/'); // set in .env e.g. http://localhost:8000
        try {
            $response = Http::timeout(5)
                ->withToken($token)
                ->accept('application/json')
                ->get("{$authUrl}/api/me");

            if ($response->failed()) {
                return response()->json(['error' => 'Invalid token or authentication failed'], 401);
            }

            // attach user info to request for controllers
            $userData = $response->json();
            // If auth-service returns { user: {...} }, normalize:
            if (isset($userData['user'])) {
                $user = $userData['user'];
            } else {
                $user = $userData;
            }
            $request->attributes->set('auth_user', $user);

            return $next($request);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Auth service not reachable', 'details' => $e->getMessage()], 500);
        }
    }
}
