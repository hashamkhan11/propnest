@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-[13px] text-primary-900']) }}>
    {{ $value ?? $slot }}
</label>
