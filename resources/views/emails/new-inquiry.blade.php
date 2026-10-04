@component('mail::message')
# New inquiry about {{ $inquiry->property->title }}

**From:** {{ $inquiry->buyer->name }} ({{ $inquiry->buyer->email }})

{{ $inquiry->message }}

@component('mail::button', ['url' => route('properties.show', $inquiry->property)])
View Listing
@endcomponent

Reply directly to {{ $inquiry->buyer->email }} to respond, or manage all your inquiries below.

@component('mail::button', ['url' => route('agent.inquiries.index'), 'color' => 'gray'])
View Inquiries
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
