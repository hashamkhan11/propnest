@component('mail::message')
# You're subscribed!

Thanks for subscribing to the {{ config('app.name') }} newsletter. We'll email you with new listings and updates.

@component('mail::button', ['url' => route('properties.index')])
Browse Listings
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
