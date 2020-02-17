<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ActivateSwarm\ActivateSwarm $this */

$this->setBaseLocation($this->requestData->postCheck("baseLocation"));
$this->setInitialLocation($this->requestData->postCheck("initialLocation"));
$this->setLibsLocation($this->requestData->postCheck("libsLocation"));
$this->setSwarmClockSpeed($this->requestData->postCheck("swarmClockSpeed"));
$this->setCheckHash($this->requestData->postCheck("checkHash"));
