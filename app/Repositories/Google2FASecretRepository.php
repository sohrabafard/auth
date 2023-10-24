<?php

namespace App\Repositories;

use App\Models\Google2FASecret;
use App\Models\User;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class Google2FASecretRepository
{
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }
    public function getLatestForUser(User $user): ?Google2FASecret
    {
        return $user->google2faSecrets()->latest()->first();
    }

    public function existsForUser(User $user): bool
    {
        return Google2FASecret::where('user_id', $user->id)->exists();
    }
    public function create(array $data): Google2FASecret
    {
        return Google2FASecret::create($data);
    }
    public function verifyKeyForUser(User $user, string $oneTimePassword): bool
    {
        $latestGoogle2FASecret = $this->getLatestForUser($user);

        if (!$latestGoogle2FASecret) {
            return false;
        }

        try {
            $google2fa_secret = $latestGoogle2FASecret->google2fa_secret;
            return $this->google2fa->verifyKey(
                $google2fa_secret,
                $oneTimePassword
            );
        } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
            return false;
        }
    }

    public function generateSecretKey(): ?string
    {
        try {
            return $this->google2fa->generateSecretKey();
        } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
        }
        return null;
    }
}

