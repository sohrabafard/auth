<?php

namespace App\Services;
use App\Models\User;
use App\Http\Resources\v1\UserResource;
use App\Repositories\Google2FASecretRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthService
{

    private UserRepository $userRepository;
    private Google2FASecretRepository $google2FASecretRepository;

    public function __construct(UserRepository $userRepository, Google2FASecretRepository $google2FASecretRepository)
    {
        $this->userRepository = $userRepository;
        $this->google2FASecretRepository = $google2FASecretRepository;
    }

    public function registerUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);  // Using Hash facade
        $user = $this->userRepository->create($data);

        $google2fa_secret = $this->google2FASecretRepository->generateSecretKey();

        // Encrypt the secret before storing
        $encrypted_secret = encrypt($google2fa_secret);

        $this->google2FASecretRepository->create([
            'user_id' => $user->id,
            'google2fa_secret' => $encrypted_secret,
        ]);

        return $user;
    }

    public function verifyTOTP(User $user, string $oneTimePassword): bool
    {
        return $this->google2FASecretRepository->verifyKeyForUser($user, $oneTimePassword);
    }

    public function login(array $data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);
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
        return $this->google2FASecretRepository->existsForUser($user);
    }
}
