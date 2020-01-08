<?php

$drives = $this->requestData->postCheck("drives");
$this->setAvailableDrives($drives);
$this->setExecuted();
$this->saveState();
