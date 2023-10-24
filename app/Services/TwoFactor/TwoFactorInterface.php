<?php

namespace App\Services\TwoFactor;

use App\Models\User;

interface TwoFactorInterface
{
    public function generate(User $user): string;
    public function verify(User $user, string $code): bool;
}
