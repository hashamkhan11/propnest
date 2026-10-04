@props(['user', 'size' => 'w-12 h-12', 'textClass' => 'font-heading font-700 text-base'])

@php
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->map(fn ($n) => $n[0] ?? '')->take(2)->implode('');
@endphp

<div {{ $attributes->merge(['class' => "$size rounded-full overflow-hidden bg-gradient-to-br from-primary-100 to-primary-50 text-primary-800 $textClass flex items-center justify-center"]) }}>
    @if ($user->profile_photo_url)
        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
    @else
        {{ $initials }}
    @endif
</div>
