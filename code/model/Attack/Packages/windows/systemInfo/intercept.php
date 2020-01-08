<?php

if (isset($_FILES["systemFile"])) {
    $contents = file_get_contents($_FILES["systemFile"]["tmp_name"]);

    $this->setSystemInfo($contents);
    $this->setExecuted();
    $this->saveState();
}