<?php

use Kelvinho\Virus\Header;

$access_token = $this->requestData->postCheck("access_token");
if ($this->getAccessToken() === $access_token) {
    $this->setExecuted();
    $this->saveState();
} else {
    Header::forbidden();
}