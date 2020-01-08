<?php

use Kelvinho\Virus\Header;
use Kelvinho\Virus\Virus\Virus;

$virus_id = $requestData->postCheck("virus_id");
$name = $requestData->postCheck("name");
$profile = $requestData->postCheck("profile");

if (!Virus::exists($virus_id)) {
    Header::badRequest();
}

if (!$authenticator->authorized($virus_id)) {
    Header::forbidden();
}

$virus = $virusFactory->get($virus_id);
$virus->setName($name);
$virus->setProfile($profile);
$virus->saveState();
