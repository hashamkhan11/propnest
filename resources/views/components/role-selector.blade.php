@props(['name', 'value' => null])

<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-2.5 sm:gap-3']) }}>
    <label class="group relative flex flex-col items-center gap-1.5 rounded-lg border-2 px-3 py-4 cursor-pointer transition-all duration-150 {{ $value === 'buyer' ? 'border-primary-600 bg-primary-50 shadow-sm shadow-primary-900/5' : 'border-gray-200 bg-white hover:border-primary-300 hover:shadow-sm hover:-translate-y-0.5' }}">
        <input type="radio" name="{{ $name }}" value="buyer" wire:model.live="{{ $name }}" class="sr-only">

        @if ($value === 'buyer')
            <span class="absolute top-2 right-2 flex items-center justify-center w-4 h-4 rounded-full bg-primary-600 text-white">
                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            </span>
        @endif

        <span class="flex items-center justify-center w-9 h-9 rounded-full transition-colors {{ $value === 'buyer' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-primary-100 group-hover:text-primary-500' }}">
            <x-icon.house-search class="w-4 h-4" />
        </span>
        <span class="font-heading font-semibold text-xs {{ $value === 'buyer' ? 'text-primary-800' : 'text-gray-700' }}">Buyer</span>
        <span class="text-[11px] text-gray-500 text-center leading-snug">Browse and save listings</span>
    </label>

    <label class="group relative flex flex-col items-center gap-1.5 rounded-lg border-2 px-3 py-4 cursor-pointer transition-all duration-150 {{ $value === 'agent' ? 'border-primary-600 bg-primary-50 shadow-sm shadow-primary-900/5' : 'border-gray-200 bg-white hover:border-primary-300 hover:shadow-sm hover:-translate-y-0.5' }}">
        <input type="radio" name="{{ $name }}" value="agent" wire:model.live="{{ $name }}" class="sr-only">

        @if ($value === 'agent')
            <span class="absolute top-2 right-2 flex items-center justify-center w-4 h-4 rounded-full bg-primary-600 text-white">
                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            </span>
        @endif

        <span class="flex items-center justify-center w-9 h-9 rounded-full transition-colors {{ $value === 'agent' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-primary-100 group-hover:text-primary-500' }}">
            <x-icon.house-key class="w-4 h-4" />
        </span>
        <span class="font-heading font-semibold text-xs {{ $value === 'agent' ? 'text-primary-800' : 'text-gray-700' }}">Agent</span>
        <span class="text-[11px] text-gray-500 text-center leading-snug">List and manage properties</span>
    </label>
</div>
