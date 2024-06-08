<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (file_exists(database_path('database.sqlite'))) {
            Schema::enableForeignKeyConstraints();
        }

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return route('auth.password.reset', [
                'email' => $user->email,
                'token' => $token,
            ]);
        });
    }
}
