<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];


    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /**
         * If you would like your client's secrets to be hashed when stored in your database, you should call the Passport::hashClientSecrets method in the boot method of your App\Providers\AuthServiceProvider class
         * Once enabled, all of your client secrets will only be displayable to the user immediately after they are created.
         * Since the plain-text client secret value is never stored in the database, it is not possible to recover the secret's value if it is lost.
         */
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(5));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::tokensCan([
            'read-data' => 'Read your data',
            'write-data' => 'Modify your data',
        ]);
    }
}
