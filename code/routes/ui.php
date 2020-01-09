<?php /** @noinspection PhpIncludeInspection */

// routes for UI
use Kelvinho\Virus\Singleton\Header;

$router->get("", function () use ($requestData) {
    if ($requestData->getHost() == DOMAIN) {
        \header("Location: " . DOMAIN_DASHBOARD);
        Header::redirect();
    } else {
        \header("Location: http://google.com");
        Header::redirect();
    }
});
$router->get("dashboard", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory) {
    include(__DIR__ . "/../view/dashboard.php");
});
$router->get("virus", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    include(__DIR__ . "/../view/virus.php");
});
$router->get("attack", function () use ($requestData, $authenticator, $session, $attackFactory) {
    include(__DIR__ . "/../view/attack.php");
});
$router->get("login", function () use ($requestData, $authenticator) {
    include(__DIR__ . "/../view/login.php");
});

// and controller stuff
$router->post("controller/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php");
});
$router->get("controller/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php");
});
