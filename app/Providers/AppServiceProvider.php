<?php

namespace App\Providers;

use App\Enums\User\UserRole;
use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        // Fail loudly in development and tests on N+1 lazy loading, typos in
        // attribute names and writes to non-fillable columns.
        Model::shouldBeStrict(! $this->app->isProduction());

        Gate::before(fn (?User $user, string $ability) => in_array($user?->role, [UserRole::Admin, UserRole::SuperAdmin], true) ? true : null);

        // Livewire actions are sent to /livewire/update, not the page's route.
        // Re-apply the page's role check there, or anyone signed in could call
        // actions on an admin component.
        Livewire::addPersistentMiddleware([
            EnsureUserHasRole::class,
        ]);
    }
}
