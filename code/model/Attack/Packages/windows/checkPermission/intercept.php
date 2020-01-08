<?php

if (isset($_FILES["permFile"])) {
    $contents = file_get_contents($_FILES["permFile"]["tmp_name"]);
    $this->setPermissions($contents);
    $this->setExecuted();
    $this->saveState();
}