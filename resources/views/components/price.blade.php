@props(['amount', 'whole' => false])

<span {{ $attributes }}>{{ \App\Support\Settings::currency()->format($amount, $whole) }}</span>
