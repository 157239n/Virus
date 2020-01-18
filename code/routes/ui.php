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
$router->get("virus", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../view/virus.php");
});
$router->get("attack", function () use ($requestData, $authenticator, $session, $attackFactory) {
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

// and controller stuff
$router->post("controller/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php");
});
$router->get("controller/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php");
});
