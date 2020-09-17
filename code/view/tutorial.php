<?php

use Kelvinho\Virus\Singleton\HtmlTemplate;

global $session, $userFactory, $authenticator;

$user_handle = $session->get("user_handle", null);
$darkMode = $user_handle === null ? false : $userFactory->get($user_handle)->isDarkMode();

?>
<html lang="en_US">
<head>
    <title>Tutorials</title>
    <?php HtmlTemplate::header($darkMode); ?>
</head>
<body>
<?php if ($authenticator->authenticated()) HtmlTemplate::topNavigation();
HtmlTemplate::body(); ?>
<h2>Tutorials</h2>
</body>
<?php HtmlTemplate::scripts(); ?>
</html>
