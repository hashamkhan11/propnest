@props(['initialView' => null, 'resetToken' => null])

<div
    x-data="{
        open: $el.dataset.initialOpen === 'true',
        view: $el.dataset.initialView,
        cameFromDirectUrl: $el.dataset.initialOpen === 'true',
        openModal(view) {
            this.view = view;
            this.open = true;
            this.cameFromDirectUrl = false;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
            if (this.cameFromDirectUrl) {
                this.cameFromDirectUrl = false;
                Livewire.navigate(this.$el.dataset.homeUrl);
            }
        },
        matchInternalLinkView(event) {
            const link = event.target.closest('a');
            if (! link) return null;
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.button !== 0) return null;
            const href = link.getAttribute('href');
            if (href === this.$el.dataset.loginUrl) return 'login';
            if (href === this.$el.dataset.registerUrl) return 'register';
            if (href === this.$el.dataset.forgotUrl) return 'forgot-password';
            return null;
        },
        handleInternalMousedown(event) {
            // The embedded auth screens' own cross-links carry `wire:navigate`
            // from when they were standalone pages. Livewire's navigate plugin
            // binds its SPA-navigation trigger directly on these anchors as a
            // `mousedown` listener (not `click`) during Alpine's init walk, so
            // by the time any `click` handler runs, the navigation is already
            // underway — removing the `wire:navigate` attribute afterward is
            // too late to stop it either way. Stopping propagation here, in the
            // capture phase (see `@mousedown.capture` below), runs before the
            // event ever reaches the anchor itself, which is the only point
            // that reliably keeps Livewire from hijacking the interaction.
            if (this.matchInternalLinkView(event)) event.stopPropagation();
        },
        handleInternalClick(event) {
            const view = this.matchInternalLinkView(event);
            if (view) { event.preventDefault(); this.view = view; }
        }
    }"
    x-init="if (open) document.body.classList.add('overflow-hidden')"
    x-on:open-auth-modal.window="openModal($event.detail.view)"
    x-on:keydown.escape.window="if (open) closeModal()"
    data-auth-modal
    data-initial-open="{{ $initialView ? 'true' : 'false' }}"
    data-initial-view="{{ $initialView ?? 'login' }}"
    data-login-url="{{ route('login') }}"
    data-register-url="{{ route('register') }}"
    data-forgot-url="{{ route('password.request') }}"
    data-home-url="{{ route('home') }}"
>
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"
        @click="closeModal()"
    ></div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 pointer-events-none">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl shadow-primary-900/10 ring-1 ring-black/5 p-8 sm:p-10 pointer-events-auto"
            role="dialog"
            aria-modal="true"
            @click.outside="closeModal()"
            @mousedown.capture="handleInternalMousedown($event)"
            @click="handleInternalClick($event)"
        >
            <button
                type="button"
                @click="closeModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                aria-label="Close"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <div x-show="view === 'login'"><livewire:pages.auth.login /></div>
            <div x-show="view === 'register'"><livewire:pages.auth.register /></div>
            <div x-show="view === 'forgot-password'"><livewire:pages.auth.forgot-password /></div>
            @if ($resetToken)
                <div x-show="view === 'reset-password'"><livewire:pages.auth.reset-password :token="$resetToken" /></div>
            @endif
        </div>
    </div>
</div>
