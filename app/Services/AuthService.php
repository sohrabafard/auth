<?php

namespace App\Services;

use App\Models\Google2FASecret;
use App\Models\User;
use App\Http\Resources\v1\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use PragmaRX\Google2FA\Google2FA;

class AuthService
{
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    public function registerUser(array $data): User
    {
        // Validation and other pre-processing can be done here
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        // Generate Google 2FA secret for the user
        $google2fa_secret = $this->google2fa->generateSecretKey();

        // Store the secret associated with the user
        Google2FASecret::create([
            'user_id' => $user->id,
            'google2fa_secret' => $google2fa_secret,
        ]);

        return $user;
    }

    public function verifyTOTP(User $user, string $oneTimePassword): bool
    {
        $latestGoogle2FASecret = Google2FASecret::getLatestForUser($user);

        if (!$latestGoogle2FASecret) {
            // No 2FA secret found for the user
            return false;
        }

        return $this->google2fa->verifyKey(
            $latestGoogle2FASecret->google2fa_secret,
            $oneTimePassword
        );
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();
        $response = [
            'user' => null,
            'requires_2fa' => false,
        ];

        if ($user && Hash::check($data['password'], $user->password)) {
            $response['user'] = $user;
            $response['requires_2fa'] = $this->requires2FA($user);
        }

        return $response;
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

    // Add to AuthService.php
    public function requires2FA(User $user): bool
    {
        return Google2FASecret::where('user_id', $user->id)->exists();
    }
}
