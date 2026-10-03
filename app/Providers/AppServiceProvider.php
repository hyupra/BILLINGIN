<?php

namespace App\Providers;

use App\Modules\Identity\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
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
        Event::listen(function (Login $event) {
            LoginHistory::create([
                'user_id' => $event->user->getAuthIdentifier(),
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
                'logged_at' => now(),
            ]);
        });
    }
}
