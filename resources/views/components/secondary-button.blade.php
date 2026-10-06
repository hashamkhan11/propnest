<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white border border-gray-300 rounded-md font-medium text-sm leading-none text-primary-900 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2 active:translate-y-px disabled:opacity-50 transition-colors']) }}>
    {{ $slot }}
</button>
