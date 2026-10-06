@props(['name', 'value' => null])

@php
    $roles = [
        'buyer' => ['label' => 'Find a home', 'hint' => 'Save homes and message agents', 'icon' => 'house-search'],
        'agent' => ['label' => 'List homes', 'hint' => 'Publish and manage listings', 'icon' => 'house-key'],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-2.5']) }}>
    @foreach ($roles as $role => $meta)
        @php $selected = $value === $role; @endphp
        <label @class([
            'relative flex flex-col gap-2 rounded-lg border px-3.5 py-3 cursor-pointer transition-colors',
            'border-primary-900 bg-cream ring-1 ring-primary-900' => $selected,
            'border-gray-300 bg-white hover:border-gray-400' => ! $selected,
        ])>
            <input type="radio" name="{{ $name }}" value="{{ $role }}" wire:model.live="{{ $name }}" class="sr-only">

            <span class="flex items-center justify-between">
                <x-dynamic-component :component="'icon.' . $meta['icon']" @class(['w-4 h-4', 'text-accent-600' => $selected, 'text-gray-400' => ! $selected]) />
                <span @class([
                    'w-3.5 h-3.5 rounded-full border flex items-center justify-center',
                    'border-primary-900' => $selected,
                    'border-gray-300' => ! $selected,
                ])>
                    @if ($selected)<span class="w-1.5 h-1.5 rounded-full bg-primary-900"></span>@endif
                </span>
            </span>
            <span>
                <span class="block text-sm font-medium text-primary-900">{{ $meta['label'] }}</span>
                <span class="block text-xs text-gray-500 mt-0.5 leading-snug">{{ $meta['hint'] }}</span>
            </span>
        </label>
    @endforeach
</div>
