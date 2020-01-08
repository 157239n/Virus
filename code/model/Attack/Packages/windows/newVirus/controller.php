<?php

$baseLocation = $this->requestData->postCheck("baseLocation");

$this->setBaseLocation($_POST["baseLocation"]);
$this->saveState();