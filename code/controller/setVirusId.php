<?php

use Kelvinho\Virus\Singleton\Header;

if (!$authenticator->authenticated()) Header::forbidden();
$session->set("virus_id", $requestData->postCheck("virus_id"));
