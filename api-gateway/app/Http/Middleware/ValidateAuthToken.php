<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidateAuthToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Unauthorized — token missing'], 401);
        }

        // cache key uses hashed token so we never store raw token as key
        $cacheKey = 'gateway:auth:token:' . hash('sha256', $token);
        $cacheTtl = (int) config('gateway.auth_cache_ttl', 30); // seconds

        // 1) Try cache first
        if (Cache::has($cacheKey)) {
            $user = Cache::get($cacheKey);
            $this->attachUserToRequest($request, $user);
            return $next($request);
        }

        // 2) Not cached → call auth-service /api/me
        $authBase = rtrim(config('gateway.services.auth', env('AUTH_SERVICE_URL', 'http://localhost:8000')), '/');
        $authUrl = $authBase . '/api/me';

        try {
            $timeout = (int) config('gateway.timeout', 5);
            $resp = Http::withToken($token)
                        ->accept('application/json')
                        ->timeout($timeout)
                        ->get($authUrl);

            // 2a) Invalid token or unauthorized
            if ($resp->status() === 401 || $resp->status() === 403) {
                return response()->json(['message' => 'Unauthorized — invalid token'], 401);
            }

            // 2b) Upstream error (5xx) or not reachable
            if ($resp->serverError() || $resp->status() >= 500) {
                Log::warning('Auth service error while validating token', ['status' => $resp->status(), 'url' => $authUrl]);
                return response()->json(['message' => 'Auth service error'], 503);
            }

            // 2c) Success -> normalize user payload
            $payload = $resp->json() ?? [];
            $user = $payload['user'] ?? $payload;

            if (empty($user) || !isset($user['id'])) {
                // unexpected response
                Log::warning('Auth service returned unexpected /me payload', ['payload' => $payload]);
                return response()->json(['message' => 'Unauthorized — invalid token payload'], 401);
            }

            // 3) cache the user for a short time to reduce calls
            Cache::put($cacheKey, $user, $cacheTtl);

            // 4) attach user info to request (and headers) before forwarding
            $this->attachUserToRequest($request, $user);

            // 5) optionally strip Authorization header before forwarding to internal services
            if (config('gateway.strip_auth_header', true)) {
                $request->headers->remove('authorization');
            }

            return $next($request);

        } catch (\Throwable $e) {
            Log::error('Exception while validating token at gateway', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Auth service unreachable'], 503);
        }
    }

    protected function attachUserToRequest(Request $request, array $user)
    {
        // Attach as request attribute for controllers (if needed)
        $request->attributes->set('auth_user', $user);

        // Also pass minimal data to downstream services via headers (string-only)
        $request->headers->set('X-Auth-User-Id', (string) ($user['id'] ?? ''));
        $request->headers->set('X-Auth-User-Email', (string) ($user['email'] ?? ''));
        // Put full user object in a single header safely (base64 JSON)
        $request->headers->set('X-Auth-User', base64_encode(json_encode($user)));
    }
}
