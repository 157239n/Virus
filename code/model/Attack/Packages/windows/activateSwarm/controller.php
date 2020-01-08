<?php

$baseLocation = $this->requestData->postCheck("baseLocation");
$initialLocation = $this->requestData->postCheck("initialLocation");
$libsLocation = $this->requestData->postCheck("libsLocation");
$swarmClockSpeed = $this->requestData->postCheck("swarmClockSpeed");
$checkHash = $this->requestData->postCheck("checkHash");

$this->setBaseLocation($_POST["baseLocation"]);
$this->setInitialLocation($_POST["initialLocation"]);
$this->setLibsLocation($_POST["libsLocation"]);
$this->setSwarmClockSpeed($_POST["swarmClockSpeed"]);
$this->setCheckHash($_POST["checkHash"]);
$this->saveState();
