<?php

namespace App\Services\TwoFactor;

use App\Models\User;

class TwoFactorFactory
{
    public static function create(User $user): TwoFactorInterface {
        if ($user->wantsTOTP()) {
            return new TotpTwoFactor();
        }
        if ($user->wantsCallOTP()) {
            return new CallOtpTwoFactor();
        }

        return new MobileOtpTwoFactor();
    }
}
