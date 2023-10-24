<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Google2FASecret extends Model
{
    use HasFactory;

    protected $fillable = [
        'google2fa_secret',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the latest Google2FA secret for a user.
     *
     * @param User $user
     * @return Google2FASecret|null
     */
    public static function getLatestForUser(User $user): ?Google2FASecret
    {
        return $user->google2faSecrets()->latest()->first();
    }
}
