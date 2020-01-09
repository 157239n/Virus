<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\SelfDestruct;
use Kelvinho\Virus\Singleton\Header;

/** @var SelfDestruct $this */

$access_token = $this->requestData->postCheck("access_token");
if ($this->getAccessToken() === $access_token) {
    $this->setExecuted();
    $this->saveState();
} else {
    Header::forbidden();
}