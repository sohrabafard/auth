<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        return $this->authService->respondWithToken($user, 'UserToken');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->validated());
        if (!$user) {
            return response()->json('Invalid email or password.', 401);
        }
        return $this->authService->respondWithToken($user, 'userToken');
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request->user());
    }
}