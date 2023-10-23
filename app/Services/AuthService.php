<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\v1\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthService
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login(array $data): ?User
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }
        return $user;
    }

    public function logout(User $user): JsonResponse
    {
        $user->tokens()->delete();
        return response()->json('Successfully logged out.');
    }

    public function respondWithToken(User $user, string $tokenName): JsonResponse
    {
        $tokenResult = $user->createToken($tokenName);
        return response()->json([
            'user' => new UserResource($user),
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer'
        ]);
    }
}