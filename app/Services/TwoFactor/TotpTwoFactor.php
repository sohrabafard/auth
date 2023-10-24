<?php

namespace App\Services\TwoFactor;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class TotpTwoFactor implements TwoFactorInterface
{
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    public function generate(User $user): string
    {
        // Generate and return TOTP secret
    }

    public function verify(User $user, string $code): bool
    {
        // Verify TOTP code
    }
}
