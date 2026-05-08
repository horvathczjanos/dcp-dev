<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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


        Passport::tokensCan([
            'badges:read' => 'Open Badge-ek olvasása',
            'profile:read' => 'Profil olvasása',
            'https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.readonly' => 'Read profile',
            'https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.update' => 'Update profile',
            'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.readonly' => 'Read assertions',
            'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.create' => 'Create assertions',
            'offline_access' => 'Refresh tokens',
        ]);

        Passport::tokensExpireIn(Carbon::now()->addHour());
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::personalAccessTokensExpireIn(Carbon::now()->addMonths(6));
    }
}
