<?php

$directories = $this->requestData->postCheck("directories");

$this->setDirectories($_POST["directories"]);
$this->saveState();