<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = $this->userRepository->getDetailsByUsername($credentials['Username']);

        if(!Hash::check($credentials['Password'], $user->password)) {
            return response()->json([
                'Status' => false,
                'Message' => 'InvalidCredential'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $token = $this->generateToken();

        $this->userRepository->update([
            'token' => $token,
            'token_last_validity_timestamp' => now()->addMinutes(config('custom_settings.token_validity'))
        ], $user->id);

        return response()->json([
            'Status' => true,
            'Message' => Response::$statusTexts[Response::HTTP_OK],
            'Data' => $user,
            'Token' => $token
        ], Response::HTTP_OK);
    }

    private function generateToken() {
        return Str::uuid();
    }

    public function logout() {
        if(!auth()->user()) {
            return response()->json([
                'Status' => false,
                'Message' => Response::$statusTexts[Response::HTTP_NOT_ACCEPTABLE]
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->userRepository->update([
            'token' => null,
            'token_last_validity_timestamp' => null
        ], auth()->user()->id);

        return response()->json([
            'Status' => true,
            'Message' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }
}
