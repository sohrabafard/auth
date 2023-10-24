<?php

namespace App\Services\TwoFactor;

use App\Models\User;

class MobileOtpTwoFactor implements TwoFactorInterface
{
    public function generate(User $user): string
    {
        // Generate and send OTP via SMS
    }

    public function verify(User $user, string $code): bool
    {
        // Verify OTP
    }
}
