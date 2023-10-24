<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Http\Resources\v1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request->validated());
        return $this->authService->respondWithToken($user, 'UserToken');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->authService->login($request->validated());

        if (!$response['user']) {
            return response()->json('Invalid email or password.', Response::HTTP_UNAUTHORIZED);
        }

        if ($response['requires_2fa']) {
            // User must verify with 2FA
            return response()->json([
                'message' => '2FA required',
                'user' => new UserResource($response['user'])],
                Response::HTTP_PRECONDITION_REQUIRED
            );
        }

        return $this->authService->respondWithToken($response['user'], 'userToken');
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        return $this->authService->logout($request->user());
    }

    public function verify(VerifyRequest $request)
    {
        $otp = $request->input('otp'); // The one-time password from the user's authenticator app
        $user = $request->user();

        if (!$this->authService->verifyTOTP($user, $otp)) {
            return response()->json('Invalid one-time password.', 401);
        }

        return response()->json('Successfully verified.');
    }
}
