<?php

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;

global $authenticator, $requestData, $timezone;

if ($authenticator->authenticated()) Header::redirectToHome(); ?>
<html lang="en_US">
<head>
    <title>Log in - Virs</title>
    <?php HtmlTemplate::header(false); ?>
</head>
<body>
<?php HtmlTemplate::body(); ?>
<h2>Log in</h2>
<label for="login_user_handle">User name</label><input id="login_user_handle" class="w3-input" type="text"><br>
<label for="login_password">Password</label><input id="login_password" class="w3-input" type="password"><br>
<button class="w3-btn w3-light-blue" onclick="login()">Login</button>
<h2>Register</h2>
<label for="register_user_handle">User name</label><input id="register_user_handle" class="w3-input" type="text"><br>
<label for="register_password">Password</label><input id="register_password" class="w3-input" type="password"><br>
<label for="register_name">Name</label><input id="register_name" class="w3-input" type="text"><br>
<label for="register_timezone">Timezone</label><select id="register_timezone" class="w3-select" name="option"
                                                       style="padding: 10px;">
    <?php foreach ($timezone->getTimezones() as $timezoneString) { ?>
        <option value="<?php echo $timezoneString; ?>"><?php echo $timezone->getDescription($timezoneString); ?></option>
    <?php } ?>
</select>
<br><br>
<button class="w3-btn w3-light-green" onclick="register()">Register</button>
<h2>What is this?</h2>
<p>Long story short, a few years ago, I made my first virus to go and spy on some people. It was mainly for
    curiosity and it felt really awesome to be doing stuff you're not supposed to be doing. But maintaining
    targets and writing raw shell codes are not fun! It wastes time, it requires brain power, and it's stupendously
    hard to actually get any spying done. So this is just that, a tool to help me and you guys use my virus. Just
    sign up above and try it out. Also please note that currently, this virus works on Windows only. But because 80%
    of laptops and desktops in the world run Windows, I think it covers well.</p>
<p>This application is under the MIT license, and is freely available over <a href="<?php echo GITHUB_PAGE; ?>"
                                                                              class="link">github</a>,
    if the technical among you want to host this on your own website or want to check the integrity and security of
    this. I have put my best efforts into securing the application, but there can still be vulnerabilities.</p>
</body>
<?php HtmlTemplate::scripts(); ?>
<script type="application/javascript">
    const gui = {
        login_user_handle: $("#login_user_handle"), login_password: $("#login_password"),
        register_user_handle: $("#register_user_handle"), register_password: $("#register_password"),
        register_name: $("#register_name"), register_timezone: $("#register_timezone"),
    };

    //gui.register_timezone.val(0);

    function login() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/login", type: "POST",
            data: {user_handle: gui.login_user_handle.val().trim(), password: gui.login_password.val().trim()},
            success: () => window.location = "<?php echo DOMAIN . "/login"; ?>",
            error: () => toast.display("User doesn't exist or password is wrong")
        });
    }

    function register() {
        const register_name = gui.register_name.val().trim();
        const register_user_handle = gui.register_user_handle.val().trim();
        const register_password = gui.register_password.val().trim();
        if (register_name.length === 0) return toast.display("Name can't be empty");
        if (register_user_handle.length === 0) return toast.display("User name can't be empty");
        if (register_user_handle.match("[^A-Za-z0-9_]")) return toast.display("User name can only be letters, numbers and \"_\".");
        if (register_user_handle.length > <?php echo NAME_LENGTH_LIMIT; ?>) toast.display("User handle exceeds max length of 20");
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/register", type: "POST", data: {
                user_handle: register_user_handle, password: register_password,
                name: register_name, timezone: gui.register_timezone.val()
            },
            success: () => toast.display("Register successful. Please log in now"),
            error: () => toast.display("Username already taken")
        })
    }

    const loginFunction = (event) => event.which === 13 ? login() : 0;
    gui.login_user_handle.keydown(loginFunction);
    gui.login_password.keydown(loginFunction);

    const registerFunction = (event) => event.which === 13 ? register() : 0;
    gui.register_user_handle.keydown(registerFunction);
    gui.register_password.keydown(registerFunction);
    gui.register_name.keydown(registerFunction);
</script>
</html>
