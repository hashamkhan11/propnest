@props(['amount'])

<span {{ $attributes }}>{{ \App\Support\Settings::currency()->format($amount) }}</span>
