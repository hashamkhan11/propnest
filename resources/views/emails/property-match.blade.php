@component('mail::message')
# New listing matches your saved search

**{{ $property->title }}** — {{ \App\Support\Settings::currency()->format($property->price) }}
{{ $property->address }}

This matches your saved search: _{{ $savedSearch->label() }}_

@component('mail::button', ['url' => route('properties.show', $property)])
View Listing
@endcomponent

Don't want these emails? You can turn off alerts for this search any time.

@component('mail::button', ['url' => route('saved-searches.index'), 'color' => 'gray'])
Manage Saved Searches
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
