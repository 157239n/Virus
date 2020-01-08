<?php

use function Kelvinho\Virus\filter;

if (isset($_FILES["envFile"])) {
    $contents = file_get_contents($_FILES["envFile"]["tmp_name"]);
    $lines = filter(explode("\n", $contents), function ($line) {
        return !empty(trim($line));
    });
    $data = [];
    foreach ($lines as $line) {
        $contents = explode("=", $line);
        $data[$contents[0]] = explode(";", $contents[1]);
    }

    $this->setEnv($data);
    $this->setExecuted();
    $this->saveState();
}