<?php

if (isset($_POST["fileNames"])) {
    global $attack;
    $attack->setFileNames($_POST["fileNames"]);
    $attack->saveState();
}