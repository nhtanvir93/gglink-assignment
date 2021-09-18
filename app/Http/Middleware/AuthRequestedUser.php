<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthRequestedUser
{
    public function handle(Request $request, Closure $next)
    {
        $token = request()->header('X-Token');

        $userRepository = resolve('App\Repositories\UserRepository');

        if (!$token) {
            return response()->json([
                'Status' => false,
                'Message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $userRepository->getDetailsByToken($token);

        if (!$user) {
            return response()->json([
                'Status' => false,
                'Message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        Auth::login($user);

        return $next($request);
    }
}
