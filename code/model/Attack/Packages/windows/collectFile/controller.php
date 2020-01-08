<?php

$fileNames = $this->requestData->postCheck("fileNames");

$this->setFileNames($fileNames);
$this->saveState();
