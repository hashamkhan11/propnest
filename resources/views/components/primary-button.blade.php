<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-primary-900 rounded-md font-medium text-sm leading-none text-white hover:bg-primary-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2 active:translate-y-px disabled:opacity-50 transition-colors']) }}>
    {{ $slot }}
</button>
