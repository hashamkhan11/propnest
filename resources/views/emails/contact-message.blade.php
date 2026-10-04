@component('mail::message')
# New contact form submission

**From:** {{ $contactMessage->name }} ({{ $contactMessage->email }})
**Subject:** {{ $contactMessage->subject }}

{{ $contactMessage->message }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
