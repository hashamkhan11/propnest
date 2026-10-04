@props(['disabled' => false, 'invalid' => false])

@php
$stateClasses = $invalid
    ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
    : 'border-gray-300 focus:border-primary-600 focus:ring-primary-600';
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => "rounded-lg shadow-sm py-2.5 text-sm disabled:bg-gray-50 disabled:cursor-not-allowed transition-colors $stateClasses"]) }}>
