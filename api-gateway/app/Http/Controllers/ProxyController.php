<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProxyController extends Controller
{
    protected $unsafeRequestHeaders = [
        'host', 'content-length', 'x-forwarded-proto', 'x-forwarded-port'
    ];

    // public function proxy(Request $request, $service, $path = '')
    // {
    //     $services = config('gateway.services', []);
    //     if (!isset($services[$service])) {
    //         return response()->json(['error' => 'Unknown service: '.$service], 404);
    //     }

    //     // choose upstream (support single url or array for simple load-balance)
    //     $upstream = $this->pickUpstream($services[$service]);

    //     // build target url (preserve /api prefix of upstream if needed)
    //     $base = rtrim($upstream, '/');
    //     $targetPath = ltrim($path, '/');
    //     $url = $base . ($targetPath ? '/'.$targetPath : '');

    //     // append query string
    //     if ($qs = $request->getQueryString()) {
    //         $url .= '?'.$qs;
    //     }

    //     // filter & forward headers (don't forward Host / Content-Length)
    //     $headers = collect($request->headers->all())
    //         ->mapWithKeys(fn($v,$k)=>[$k => implode(',', $v)])
    //         ->except($this->unsafeRequestHeaders)
    //         ->toArray();

    //     // log for debugging (optimize/remove in prod)
    //     Log::info('Gateway proxying', ['method'=>$request->method(), 'url'=>$url, 'headers'=>$headers]);

    //     // prepare options and send
    //     $options = [
    //         'timeout' => config('gateway.timeout', 5),
    //     ];

    //     // body handling: prefer json if content-type JSON
    //     $method = strtoupper($request->method());
    //     if (in_array($method, ['GET','HEAD','OPTIONS'])) {
    //         $response = Http::withHeaders($headers)->send($method, $url, $options);
    //     } else {
    //         $contentType = $request->header('content-type', '');
    //         if (str_contains($contentType, 'application/json')) {
    //             $options['json'] = $request->json()->all();
    //         } else {
    //             $options['body'] = $request->getContent();
    //         }
    //         $response = Http::withHeaders($headers)->send($method, $url, $options);
    //     }

    //     // filter response headers
    //     $responseHeaders = $this->filterResponseHeaders($response->headers());
    //     return response($response->body(), $response->status())->withHeaders($responseHeaders);
    // }

    public function proxy(Request $request, $service, $path = '')
    {
        // Safety: if somehow $service came in as 'api' or wrong, try to recover:
        if ($service === 'api') {
            $segments = explode('/', ltrim($request->path(), '/'));
            $service = $segments[1] ?? null;
            $path = isset($segments[2]) ? implode('/', array_slice($segments, 2)) : '';
        }

        $services = config('gateway.services', []);
        if (!isset($services[$service])) {
            return response()->json(['error' => 'Unknown service: '.$service], 404);
        }

        $upstream = $this->pickUpstream($services[$service]);
        $base = rtrim($upstream, '/');
        $targetPath = ltrim($path, '/');

        // IMPORTANT: most microservices expose their APIs under /api/...,
        // so forward to upstream's /api/<targetPath>
        $url = $base . '/api' . ($targetPath ? '/'.$targetPath : '');

        if ($qs = $request->getQueryString()) {
            $url .= '?'.$qs;
        }

        $headers = collect($request->headers->all())
            ->mapWithKeys(fn($v,$k)=>[$k => implode(',', $v)])
            ->except($this->unsafeRequestHeaders)
            ->toArray();

        Log::info('Gateway proxying', ['method'=>$request->method(), 'service'=>$service, 'url'=>$url]);

        $options = [
            'timeout' => config('gateway.timeout', 5),
        ];

        $method = strtoupper($request->method());
        if (in_array($method, ['GET','HEAD','OPTIONS'])) {
            $response = Http::withHeaders($headers)->send($method, $url, $options);
        } else {
            $contentType = $request->header('content-type', '');
            if (str_contains($contentType, 'application/json')) {
                $options['json'] = $request->json()->all();
            } else {
                $options['body'] = $request->getContent();
            }
            $response = Http::withHeaders($headers)->send($method, $url, $options);
        }

        $responseHeaders = $this->filterResponseHeaders($response->headers());
        return response($response->body(), $response->status())->withHeaders($responseHeaders);
    }

    protected function filterResponseHeaders(array $headers)
    {
        $remove = ['transfer-encoding','content-encoding','set-cookie'];
        $out = [];
        foreach ($headers as $k => $v) {
            if (in_array(strtolower($k), $remove)) continue;
            $out[$k] = is_array($v) ? implode(',', $v) : $v;
        }
        return $out;
    }

    protected function pickUpstream($upstream)
    {
        // support either string or array (basic random balancing)
        if (is_array($upstream)) {
            return $upstream[array_rand($upstream)];
        }
        return $upstream;
    }
}
