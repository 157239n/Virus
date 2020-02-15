<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir;

/** @var ExploreDir $this */

$this->requestData->moveFile("dirsFile", DATA_FILE . "/attacks/" . $this->getAttackId() . "/dirs.txt");
$this->setExecuted();
$this->saveState();
