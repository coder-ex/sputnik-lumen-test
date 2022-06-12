@component('mail::message')
# Здравствуйте {{ $user->name }},
( токен: {{$user->profile->verify_token}} )

для подтверждения регистрации, перейдите по ссылке:

@component('mail::button', ['url' => route('register.verify', ['token' => $user->profile->verify_token])])
    Подтверждение Email
@endcomponent

C Уважением,<br>
{{ config('app.name') }}
@endcomponent
