<?php

namespace App\Providers;

use App\Enums\User\UserRole;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(fn (?Authenticatable $user, string $ability) => $user?->role === UserRole::Admin ? true : null);
    }
}
