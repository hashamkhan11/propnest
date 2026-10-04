<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\SocialRoleSelection;
use App\Livewire\Public\Home;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Route::get('register', Home::class)
        ->name('register');

    Route::get('login', Home::class)
        ->name('login');

    Route::get('forgot-password', Home::class)
        ->name('password.request');

    Route::get('reset-password/{token}', Home::class)
        ->name('password.reset');

    Route::get('auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');

    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');

    Route::get('auth/social-role', SocialRoleSelection::class)
        ->name('auth.social-role');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
