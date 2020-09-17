<?php /** @noinspection PhpIncludeInspection */

// routes for UI
use Kelvinho\Virus\Singleton\Header;
use function Kelvinho\Virus\goodPath;

global $router, $requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $timezone, $mysqli, $packageRegistrar, $whitelistFactory, $demos;

$router->get("", function () use ($requestData) {
    if (!$requestData->rightHost()) Header::notFound();
    \header("Location: " . DOMAIN . "/dashboard");
    Header::redirect();
});
$router->get("dashboard", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $timezone, $demos) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/dashboard.php");
});
$router->get("virus", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $mysqli, $packageRegistrar, $timezone, $demos) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/virus.php");
});
$router->get("attack", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $packageRegistrar, $timezone) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/attack.php");
});
$router->get("login", function () use ($requestData, $authenticator, $timezone) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/login.php");
});
$router->get("faq", function () use ($requestData, $authenticator) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/faq.php");
});
$router->get("tutorials", function () use ($requestData, $authenticator) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/tutorial.php");
});
$router->get("profile", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $timezone) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/profile.php");
});
$router->get("logout", function () {
    session_destroy();
    echo "<script>window.location = '" . DOMAIN . "'</script>";
});

// and resources
$router->get("resources/images/*", function () use ($requestData) {
    if (!$requestData->rightHost()) Header::notFound();
    if ($filePath = goodPath(__DIR__ . "/../resources/images/", $requestData->getExplodedPath()[2])) {
        \header("Content-type: " . mime_content_type($filePath));
        \header("Cache-Control: private");
        header_remove("Pragma");
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
$router->get("test", function() use ($whitelistFactory, $requestData, $packageRegistrar, $mysqli) {
    include(__DIR__ . "/../test.php");
});
