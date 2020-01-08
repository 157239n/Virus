<?php

if (isset($_FILES["dirsFile"])) {
    exec("mv \"" . $_FILES["dirsFile"]["tmp_name"] . "\" " . DATA_FILE . "/attacks/" . $this->getAttackId() . "/dirs.txt");
    $this->setExecuted();
    $this->saveState();
}