<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = request()->header('X-API-KEY');

        if($apiKey != config('custom_settings.api_key')) {
            return response()->json([
                'Status' => false,
                'Message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
