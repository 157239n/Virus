<?php /** @noinspection PhpIncludeInspection */

// routes for UI
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
$router->get("shop", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/shop.php");
});
$router->get("profile", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/profile.php");
});

// and resources
$router->get("resources/images/*", function () use ($requestData) {
    if (!$requestData->rightHost()) Header::notFound();
    if ($filePath = goodPath(__DIR__ . "/../resources/images/", $requestData->getExplodedPath()[2])) {
        \header("Content-type: " . mime_content_type($filePath));
        readfile($filePath);
    } else Header::notFound();
});

// and cli stuff, only allowing local processes to invoke this
$router->getMulti(["cli/*", "cli/*/*", "cli/*/*/*"], function() use ($requestData, $userFactory, $virusFactory, $attackFactory, $whitelistFactory, $mysqli) {
    $whitelist = $whitelistFactory->new();
    $whitelist->addIp("localhost");
    if (!$whitelist->allowed($requestData->getRemoteIp()))
        $requestData->rightHost() ? Header::redirectToHome() : Header::notFound();
    include(__DIR__ . "/../cli/" . $requestData->getExplodedPath()[1] . ".php");
});
$router->get("test", function() use ($whitelistFactory, $requestData, $packageRegistrar) {
    include(__DIR__ . "/../test.php");
});
