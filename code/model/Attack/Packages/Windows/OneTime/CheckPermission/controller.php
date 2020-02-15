<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CheckPermission\CheckPermission;

/** @var CheckPermission $this */

$this->setDirectories($this->requestData->postCheck("directories"));
$this->saveState();