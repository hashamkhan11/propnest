@component('mail::message')
# Re: {{ $contactMessage->subject }}

Hi {{ $contactMessage->name }},

{{ $contactMessage->reply }}

---

**Your original message:**

{{ $contactMessage->message }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
