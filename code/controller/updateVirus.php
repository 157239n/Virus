<?php

use Kelvinho\Virus\Singleton\Header;

global $requestData, $authenticator, $virusFactory;

$virus_id = $requestData->postCheck("virus_id");

if (!$virusFactory->exists($virus_id)) Header::badRequest();
if (!$authenticator->authorized($virus_id)) Header::forbidden();
$virusFactory->get($virus_id)->setName($requestData->postCheck("name"))->setProfile($requestData->postCheck("profile"))->saveState();
