@component('mail::message')
# Payment Refunded

Hi {{ $payment->agent->name }},

Your payment of {{ number_format($payment->amount / 100, 2) }} for featuring **{{ $payment->property->title }}** has been refunded. This listing is no longer featured.

@component('mail::button', ['url' => route('agent.properties.index')])
View My Listings
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
