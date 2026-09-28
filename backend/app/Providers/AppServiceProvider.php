<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Форма «Хочу в календарь»: не больше 5 запросов за 10 минут и 20 в сутки с одного IP
        RateLimiter::for('leads', fn (Request $request) => [
            Limit::perMinutes(10, 5)->by('leads-10m:' . $request->ip()),
            Limit::perDay(20)->by('leads-day:' . $request->ip()),
        ]);
    }
}
