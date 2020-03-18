<?php

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Timezone;
use function Kelvinho\Virus\map;

if (!$authenticator->authenticated()) Header::redirectToHome();
$user = $userFactory->get($session->getCheck("user_handle"));
?>
<!DOCTYPE html>
<html lang="en_US">
<head>
    <title>Account</title>
    <?php HtmlTemplate::header(); ?>
    <style>
        select option {
            height: 200px;
        }
    </style>
</head>
<body>
<?php HtmlTemplate::topNavigation(null, null, null, null, $user->isHold()); ?>
<h2>Account</h2>
<label for="user_handle">User name</label><input id="user_handle" class="w3-input" type="text"
                                                 value="<?php echo $user->getHandle(); ?>" disabled>
<br>
<label for="name">Name</label><input id="name" class="w3-input" type="text"
                                     value="<?php echo $user->getName(); ?>">
<br>
<label for="timezone">Timezone</label><select id="timezone" class="w3-select" name="option" style="padding: 10px;">
    <?php map(Timezone::getDescriptions(), function ($description, $timezone) { ?>
        <option value="<?php echo "$timezone"; ?>"><?php echo "UTC $timezone: $description"; ?></option>
    <?php }); ?>
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
    const gui = {timezone: $("#timezone"), name: $("#name")};
    gui.timezone.val(<?php echo $user->getTimezone(); ?> +0);

    function update() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/updateUser",
            type: "POST",
            data: {
                name: gui.name.val(),
                timezone: gui.timezone.val()
            },
            success: () => window.location = "<?php echo DOMAIN . "/profile"; ?>"
        });
    }
</script>
</html>
