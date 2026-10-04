@component('mail::message')
# You're verified!

Hi {{ $agent->name }}, your agent account has been verified by our team. Your profile and listings now display a verified badge to buyers.

@component('mail::button', ['url' => route('agent.dashboard')])
Go to My Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
