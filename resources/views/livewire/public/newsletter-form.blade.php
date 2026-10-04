<div>
    <form wire:submit="subscribe" class="flex flex-col gap-2 max-w-md">
        <x-input wire:model="email" type="email" placeholder="Your email address" class="text-gray-900 placeholder:text-gray-400 w-full" />
        <x-input-error :messages="$errors->get('email')" />

        <x-button type="submit" variant="accent" class="w-full justify-center focus:!ring-0 focus:!ring-offset-0">Subscribe</x-button>
    </form>

    @if (session('success'))
        <p class="text-sm text-accent-300 mt-2">{{ session('success') }}</p>
    @endif
</div>
