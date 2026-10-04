<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

/**
 * Auto-registered via Laravel's event discovery (app/Listeners is scanned by default).
 * Do NOT also register this in AppServiceProvider::boot() — it would fire twice per
 * login, and the second pass would read the last_login_at this listener just stamped,
 * flipping is_first_login to false for genuine first-time users.
 */
class RecordUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        session(['is_first_login' => is_null($user->last_login_at)]);

        $user->forceFill(['last_login_at' => now()])->save();
    }
}
