<?php

namespace App\Services\TwoFactor;

use App\Models\User;

class CallOtpTwoFactor implements TwoFactorInterface
{
    public function generate(User $user): string
    {
        // Generate and send OTP via ISABEL ( Astrix ) call
    }

    public function verify(User $user, string $code): bool
    {
        // Verify OTP
    }
}
