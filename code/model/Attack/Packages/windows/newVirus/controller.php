<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\NewVirus;

/** @var NewVirus $this */

$this->setBaseLocation($this->requestData->postCheck("baseLocation"));
$this->saveState();