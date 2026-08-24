<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Support\CurrentInstitution;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrentInstitution::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by(
                strtolower((string) $request->input('login')).'|'.$request->ip()
            );
        });
    }
}
