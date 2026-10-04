@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6']) }}>
    @isset($title)
        <h3 class="font-heading font-700 text-lg text-gray-900 mb-4">{{ $title }}</h3>
    @endisset

    {{ $slot }}
</div>
