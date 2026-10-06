@props(['disabled' => false, 'invalid' => false])

@php
$stateClasses = $invalid
    ? 'border-red-500 focus:border-red-600 focus:ring-1 focus:ring-red-600'
    : 'border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900';
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => "bg-white rounded-md py-2.5 text-sm disabled:bg-gray-50 disabled:cursor-not-allowed transition-colors $stateClasses"]) }}>
