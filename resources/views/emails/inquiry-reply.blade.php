@component('mail::message')
# Reply about {{ $inquiry->property->title }}

**From:** {{ $inquiry->agent->name }}

{{ $inquiry->reply }}

---

**Your original message:**

{{ $inquiry->message }}

@component('mail::button', ['url' => route('properties.show', $inquiry->property)])
View Listing
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
