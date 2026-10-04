<?php

namespace App\Http\Controllers\Auth;

use App\Enums\User\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google sign-in was cancelled or failed. Please try again.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            if ($user->status === UserStatus::Suspended) {
                return redirect()->route('login')->with('error', 'This account has been suspended. Contact support for help.');
            }

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'))->with('success', 'Signed in with Google.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if ($user->status === UserStatus::Suspended) {
                return redirect()->route('login')->with('error', 'This account has been suspended. Contact support for help.');
            }

            $emailVerifiedByGoogle = $googleUser->user['email_verified'] ?? $googleUser->user['verified_email'] ?? false;

            if (! $emailVerifiedByGoogle) {
                return redirect()->route('login')->with('error', 'Google could not confirm this email address is verified. Please log in with your password instead.');
            }

            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'))->with('success', 'Signed in with Google.');
        }

        session([
            'social_pending_user' => [
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'there'),
                'email' => $googleUser->getEmail(),
                'avatar_url' => $googleUser->getAvatar(),
            ],
        ]);

        return redirect()->route('auth.social-role');
    }
}
