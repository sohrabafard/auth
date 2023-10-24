<?php

namespace App\Traits;

use App\Models\Google2FASecret;

trait HasGoogle2FASecrets
{
    public function google2faSecrets()
    {
        return $this->hasMany(Google2FASecret::class);
    }
    public function has2FAEnabled() :bool
    {
        return Google2FASecret::getLatestForUser($this) !== null;
    }

}
