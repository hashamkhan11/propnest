@props(['user', 'size' => 'w-12 h-12', 'textClass' => 'font-medium text-base'])

@php
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->map(fn ($n) => $n[0] ?? '')->take(2)->implode('');
    // A stable, muted tint per person so initials avatars are easy to tell apart.
    $tints = ['bg-[#EDE6DA] text-[#5C4A2E]', 'bg-[#E3E9E4] text-[#34503D]', 'bg-[#E6E4EE] text-[#463F66]', 'bg-[#F1E2DA] text-[#7A3A1C]', 'bg-[#E1E8EE] text-[#2F4A5E]'];
    $tint = $tints[crc32($user->name) % count($tints)];
@endphp

<div {{ $attributes->merge(['class' => "$size rounded-full overflow-hidden $tint $textClass flex items-center justify-center uppercase"]) }}>
    @if ($user->profile_photo_url)
        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
    @else
        {{ $initials }}
    @endif
</div>
