@component('mail::message')
# Your listing was rejected

**{{ $property->title }}** has been rejected by our moderation team and is no longer visible to buyers.

**Reason:** {{ $reason }}

Please review and update your listing, then republish it from My Listings.

@component('mail::button', ['url' => route('agent.properties.index')])
View My Listings
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
