<?php

use Kelvinho\Virus\Header;

$virus_id = $requestData->postCheck("virus_id");
$attack_id = $requestData->postCheck("attack_id");

if (!$authenticator->authorized($virus_id, $attack_id)) {
    Header::forbidden();
}

$session->set("virus_id", $virus_id);
$session->set("attack_id", $attack_id);
$attack = $attackFactory->get($attack_id);
$attack->cancel();
$attack->saveState();
Header::ok();
