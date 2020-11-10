<?php

use Kelvinho\Virus\Singleton\HtmlTemplate;

global $session, $userFactory, $authenticator;

?>
<html lang="en_US">
<head>
    <title>Tutorials</title>
    <?php HtmlTemplate::header($darkMode = ($user = $userFactory->current()) === null ? false : $user->isDarkMode()); ?>
</head>
<body>
<?php if ($authenticator->authenticated()) HtmlTemplate::topNavigation();
HtmlTemplate::body(); ?>
<h2>Tutorials</h2>
</body>
<?php HtmlTemplate::scripts(); ?>
</html>
