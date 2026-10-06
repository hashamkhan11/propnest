<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <h1 class="display text-[2.5rem] mb-2">Check your inbox</h1>
    <p class="text-[15px] text-gray-600 mb-8 leading-relaxed">We sent a link to <span class="text-primary-900 font-medium">{{ auth()->user()->email }}</span>. Open it to confirm your email and you are in.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 flex items-start gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm text-emerald-800">
            <x-heroicon-o-check class="w-4 h-4 mt-0.5 shrink-0" />
            {{ __('A fresh link is on its way.') }}
        </div>
    @endif

    <x-button wire:click="sendVerification" type="button" class="w-full justify-center">
        {{ __('Send the link again') }}
    </x-button>

    <p class="text-sm text-gray-600 text-center mt-8">
        {{ __('Wrong account?') }}
        <button wire:click="logout" type="button" class="text-primary-900 font-medium link-underline">{{ __('Sign out') }}</button>
    </p>
</div>
