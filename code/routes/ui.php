<?php /** @noinspection PhpIncludeInspection */

// routes for UI
use Kelvinho\Virus\Singleton\Header;
use function Kelvinho\Virus\goodPath;

global $router, $requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $timezone, $mysqli, $packageRegistrar, $whitelistFactory, $demos;

$router->getMulti(["", "index.html"], fn() => $requestData->rightHost() ? (\header("Location: " . DOMAIN . "/dashboard") & Header::redirect()) : Header::notFound());
$router->get("dashboard", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/dashboard.php") : Header::notFound());
$router->get("virus", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/virus.php") : Header::notFound());
$router->get("attack", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/attack.php") : Header::notFound());
$router->get("login", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/login.php") : Header::notFound());
$router->get("faq", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/faq.php") : Header::notFound());
$router->get("tutorials", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/tutorial.php") : Header::notFound());
$router->get("profile", fn() => $requestData->rightHost() ? include(__DIR__ . "/../view/profile.php") : Header::notFound());
$router->get("logout", fn() => session_destroy() xor print("<script>window.location = '" . DOMAIN . "'</script>"));
$router->get("ping", fn() => $requestData->rightHost() ? "" : Header::notFound());

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
$router->getMulti(["cli/*", "cli/*/*", "cli/*/*/*"], function () use ($requestData, $userFactory, $virusFactory, $attackFactory, $whitelistFactory, $mysqli) {
    ($whitelist = $whitelistFactory->new())->addIp("localhost");
    if (!$whitelist->allowed($requestData->getRemoteIp())) $requestData->rightHost() ? Header::redirectToHome() : Header::notFound();
    include(__DIR__ . "/../cli/" . $requestData->getExplodedPath()[1] . ".php");
});
$router->get("test", fn() => include(__DIR__ . "/../test.php"));

$router->get("test/a/b", function () use ($requestData) {
    var_dump(\Kelvinho\Virus\filter(explode("/", $requestData->serverVariables["SCRIPT_NAME"]), fn($el) => $el));
});
