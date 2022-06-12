@component('mail::message')
# Здравствуйте {{ $user->name ? $user->name : $user->email }},

для подтверждения регистрации, перейдите по ссылке:

@component('mail::button', ['url' => $url.'/'.$user->activation_link, ['link' => $user->activation_link]])
Подтверждение Email
@endcomponent

C Уважением,<br>
{{ config('app.name') }}
@endcomponent