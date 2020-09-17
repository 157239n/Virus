<?php

use Kelvinho\Virus\Singleton\Header;

global $requestData, $authenticator, $virusFactory;

$virus_id = $requestData->postCheck("virus_id");

if (!$authenticator->authorized($virus_id)) Header::forbidden();

$virusFactory->get($virus_id)->delete();
Header::ok();
