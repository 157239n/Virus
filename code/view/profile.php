<?php

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;

global $authenticator, $session, $userFactory, $timezone;

if (!$authenticator->authenticated()) Header::redirectToHome();
$user = $userFactory->currentChecked();
?>
<!DOCTYPE html>
<html lang="en_US">
<head>
    <title>Account - Virs</title>
    <?php HtmlTemplate::header($user->isDarkMode()); ?>
    <style>
        select option {
            height: 200px;
        }
    </style>
</head>
<body>
<?php HtmlTemplate::topNavigation(null, null, null, null, $user->isHold());
HtmlTemplate::body(); ?>
<h2>Account</h2>
<label for="user_handle">User name</label><input id="user_handle" class="w3-input" type="text"
                                                 value="<?php echo $user->getHandle(); ?>" disabled>
<br>
<label for="name">Name</label><input id="name" class="w3-input" type="text"
                                     value="<?php echo $user->getName(); ?>">
<br>
<label for="theme">Theme</label><select name="theme" id="theme" class="w3-select">
    <option value="0">Light</option>
    <option value="1">Dark</option>
</select>
<br><br>
<label for="timezone">Timezone</label><select id="timezone" class="w3-select" name="option"
                                              style="padding: 10px;">
    <?php foreach ($timezone->getTimezones() as $timezoneString) { ?>
        <option value="<?php echo $timezoneString; ?>"><?php echo $timezone->getDescription($timezoneString); ?></option>
    <?php } ?>
</select>
<br><br>
<button class="w3-btn w3-teal" onclick="update()">Update</button>
<br><br>
<label>Usage this month</label>
<?php $user->usage()->display(); ?>
<p>Above is the approximate resource you consume this month. Every month you have a free $10 credit. After you have
    spent that free portion, you will have to enter in your payment details or you won't be able to launch new attacks.
    After that, you can still launch attacks as usual. We won't charge you until you accumulate $5.</p>
<div id="paypal-button-1"></div>
<div id="paypal-button-2"></div>
</body>
<?php HtmlTemplate::scripts(); ?>
<script>
    const gui = {timezone: $("#timezone"), name: $("#name"), theme: $("#theme")};
    gui.timezone.val("<?php echo $user->getTimezone(); ?>");
    gui.theme.val(<?php echo $user->isDarkMode() ? "1" : "0"; ?>)

    function update() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/updateUser", type: "POST",
            data: {name: gui.name.val(), timezone: gui.timezone.val(), theme: gui.theme.val()},
            success: () => window.location = "<?php echo DOMAIN . "/profile"; ?>"
        });
    }
</script>
</html>
