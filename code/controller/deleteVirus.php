<?php

use Kelvinho\Virus\Header;

$virus_id = $requestData->postCheck("virus_id");

if (!$authenticator->authorized($virus_id)) {
    Header::forbidden();
}

$session->set("virus_id", $virus_id);
$virus = $virusFactory->get($virus_id);
$virus->delete();
Header::ok();
