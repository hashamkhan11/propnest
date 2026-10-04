@component('mail::message')
# Your listing is under review

**{{ $property->title }}** has been temporarily pulled from public listings while our team reviews it. No action is needed from you right now — we'll follow up once the review is complete.

@component('mail::button', ['url' => route('agent.properties.index')])
View My Listings
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
