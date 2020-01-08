<?php

if (isset($_FILES["dataFile"]) && isset($_FILES["errFile"])) {
    $data = file_get_contents($_FILES["dataFile"]["tmp_name"]);
    $error = file_get_contents($_FILES["errFile"]["tmp_name"]);

    $this->setData($data);
    $this->setError($error);
    $this->setExecuted();
    $this->saveState();
}