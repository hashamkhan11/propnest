<div>
    <form wire:submit="subscribe" class="flex max-w-md rounded-md bg-white/5 ring-1 ring-white/15 focus-within:ring-white/40 transition">
        <label for="newsletter-email" class="sr-only">Email address</label>
        <input id="newsletter-email" wire:model="email" type="email" placeholder="you@example.com" class="flex-1 min-w-0 bg-transparent border-0 text-sm text-white placeholder:text-gray-500 focus:ring-0 px-3.5 py-2.5">
        <button type="submit" class="shrink-0 m-1 px-3.5 rounded bg-white text-primary-900 text-sm font-medium hover:bg-accent-100 transition-colors">Subscribe</button>
    </form>
    <x-input-error :messages="$errors->get('email')" class="mt-2" />

    @if (session('success'))
        <p class="text-sm text-accent-300 mt-2">{{ session('success') }}</p>
    @endif
</div>
