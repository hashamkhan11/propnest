<?php

namespace App\Http\Middleware;

use App\Enums\User\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Login already refuses suspended accounts, but a user suspended while signed
 * in would otherwise keep their session. This ends it on their next request.
 */
class LogoutSuspendedUsers
{
    public const MESSAGE = 'This account has been suspended. Contact support for help.';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->status !== UserStatus::Suspended) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->hasHeader('X-Livewire')) {
            abort(403, self::MESSAGE);
        }

        return redirect()->route('login')->with('error', self::MESSAGE);
    }
}
