<?php $__env->startComponent('mail::message'); ?>
# Cześć <?php echo new \Illuminate\Support\EncodedHtmlString($user->first_name ?? $user->name); ?>!

Twoje konto w **MiniStrava** jest gotowe. Możesz już:

- logować się do aplikacji i tworzyć aktywności,
- uzupełnić profil (avatar, bio, parametry treningowe),
- dodać znajomych i śledzić ich feed.

<?php $__env->startComponent('mail::button', ['url' => config('app.frontend_url', config('app.url'))]); ?>
Przejdź do aplikacji
<?php echo $__env->renderComponent(); ?>

Jeśli to nie Ty zakładałeś konto, zignoruj tę wiadomość.

Pozdrawiamy,<br>
Zespół MiniStrava
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /Users/sami/apistrava/api-strava/resources/views/emails/welcome.blade.php ENDPATH**/ ?>