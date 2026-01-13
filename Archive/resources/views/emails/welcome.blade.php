@component('mail::message')
# Cześć {{ $user->first_name ?? $user->name }}!

Twoje konto w **MiniStrava** jest gotowe. Możesz już:

- logować się do aplikacji i tworzyć aktywności,
- uzupełnić profil (avatar, bio, parametry treningowe),
- dodać znajomych i śledzić ich feed.

@component('mail::button', ['url' => config('app.frontend_url', config('app.url'))])
Przejdź do aplikacji
@endcomponent

Jeśli to nie Ty zakładałeś konto, zignoruj tę wiadomość.

Pozdrawiamy,<br>
Zespół MiniStrava
@endcomponent
