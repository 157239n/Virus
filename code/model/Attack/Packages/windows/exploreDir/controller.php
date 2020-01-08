<?php

$dir = $this->requestData->postCheck("dir");
$depth = $this->requestData->postCheck("depth");

$this->setRootDir($dir);
$this->setMaxDepth($depth);
$this->saveState();