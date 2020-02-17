<?php

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Timezone;
use function Kelvinho\Virus\map;

if ($authenticator->authenticated()) Header::redirectToHome(); ?>
<html lang="en_US">
<head>
    <title>Log in</title>
    <?php echo HtmlTemplate::header(); ?>
</head>
<body>
<h1>Log in</h1>
<br>
<label for="login_user_handle">User name</label><input id="login_user_handle" class="w3-input" type="text">
<br>
<label for="login_password">Password</label><input id="login_password" class="w3-input" type="password">
<div style="color: red;"><?php echo $requestData->get("loginMessage", ""); ?></div>
<h1>Register</h1>
<br>
<label for="register_user_handle">User name</label><input id="register_user_handle" class="w3-input" type="text">
<br>
<label for="register_password">Password</label><input id="register_password" class="w3-input" type="password">
<br>
<label for="register_name">Name</label><input id="register_name" class="w3-input" type="text">
<br>
<div>Timezone</div>
<div class="w3-dropdown-hover w3-light-grey" style="margin-right: 15px;">
    <button id="register_timezone" class="w3-button" style="width: 100%;">Choose timezone (UTC+0 default)</button>
    <div class="w3-dropdown-content w3-bar-block w3-border">
        <?php map(Timezone::getDescriptions(), function ($description, $timezone) { ?>
            <a onclick="changeTimezone('<?php echo "$timezone"; ?>')"
               class="w3-bar-item w3-button" style="width: 400px;"><?php echo "UTC $timezone: $description"; ?></a>
        <?php }); ?>
    </div>
</div>
<div id="register_message" style="color: red;"><?php echo $requestData->get("registerMessage", ""); ?></div>
<h1>What is this?</h1>
<p>Oh hi there, I guess you're new around here?</p>
<p>Long story short, a few years ago, I made my first virus to go and spy on some people. It was mainly for
    curiosity and it felt really awesome to be doing stuff you're not supposed to be doing. But maintaining
    targets and writing raw shell codes are not fun! It wastes time, it requires brain power, and it's stupendously
    hard to actually get any spying done. So this is just that, a tool to help me and you guys use my virus. Just
    sign up above and try it out. Also please note that currently, this virus works on Windows only. But because 80%
    of laptops and desktops in the world run Windows, I think it covers well.</p>
<p>This application is under the MIT license, and is freely available over <a href="<?php echo GITHUB_PAGE; ?>"
                                                                              style="color: blue; cursor: pointer;">github</a>,
    if the technical among you want to host this on your own website or want to check the integrity and security of
    this. I have put my best efforts into securing the application, but there can still be vulnerabilities.</p>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
<script type="application/javascript">
    const gui = {
        login_user_handle: $("#login_user_handle"),
        login_password: $("#login_password"),
        register_user_handle: $("#register_user_handle"),
        register_password: $("#register_password"),
        register_name: $("#register_name"),
        register_message: $("#register_message"),
        register_timezone: $("#register_timezone")
    };

    let timezone = 0;
    const timezones = {
        <?php echo join(",", map(Timezone::getDescriptions(), function ($description, $offset) {
            return "'$offset': '$description'";
        })); ?>
    };

    function changeTimezone(newTimezone) {
        gui.register_timezone.html("UTC " + newTimezone + " " + timezones[newTimezone]);
        timezone = newTimezone;
    }

    function login() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/login",
            type: "POST",
            data: {
                user_handle: gui.login_user_handle.val().trim(),
                password: gui.login_password.val().trim()
            },
            success: function () {
                window.location = "<?php echo DOMAIN_LOGIN; ?>";
            },
            error: function () {
                window.location = "<?php echo DOMAIN_LOGIN; ?>?loginMessage=User%20doesn't%20exist%20or%20password%20is%20wrong";
            }
        });
    }

    function register() {
        const register_name = gui.register_name.val().trim();
        const register_user_handle = gui.register_user_handle.val().trim();
        const register_password = gui.register_password.val().trim();
        if (register_name.length === 0) {
            gui.register_message.html("Name can't be empty");
            return;
        }
        if (register_user_handle.length === 0) {
            gui.register_message.html("User name can't be empty");
            return;
        }
        if (register_user_handle.match("[^A-Za-z0-9_]")) {
            gui.register_message.html("User name can only be letters, numbers and \"_\".");
            return;
        }
        if (register_user_handle.length > <?php echo NAME_LENGTH_LIMIT; ?>) {
            gui.register_message.html("User handle exceeds max length of 20");
            return;
        }
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/register",
            type: "POST",
            data: {
                user_handle: register_user_handle,
                password: register_password,
                name: register_name,
                timezone: timezone
            },
            success: function () {
                window.location = "<?php echo DOMAIN_LOGIN; ?>?registerMessage=Register%20successful.%20Please%20log%20in%20now";
            },
            error: function () {
                window.location = "<?php echo DOMAIN_LOGIN; ?>?registerMessage=Username%20already%20taken";
            }
        })
    }

    const loginFunction = function (event) {
        if (event.which === 13) {
            login();
        }
    };
    gui.login_user_handle.keydown(loginFunction);
    gui.login_password.keydown(loginFunction);
    const registerFunction = function (event) {
        if (event.which === 13) {
            register();
        }
    };
    gui.register_user_handle.keydown(registerFunction);
    gui.register_password.keydown(registerFunction);
    gui.register_name.keydown(registerFunction);
</script>
</html>
