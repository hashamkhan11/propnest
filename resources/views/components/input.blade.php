@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900 rounded-md text-sm w-full transition-colors duration-150']) }}>
