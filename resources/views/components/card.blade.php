@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-900/10 p-5 sm:p-6']) }}>
    @isset($title)
        <h3 class="font-semibold text-[15px] text-primary-900 mb-4">{{ $title }}</h3>
    @endisset

    {{ $slot }}
</div>
