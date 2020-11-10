<?php

use Kelvinho\Virus\Singleton\Header;

global $requestData, $authenticator;

$authenticator->authenticate($requestData->postCheck("user_handle"), $requestData->postCheck("password"));
$authenticator->authenticated() ? Header::ok() : Header::forbidden();
