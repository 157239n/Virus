<?php

if (isset($_POST["dir"]) && isset($_POST["depth"])) {
    global $attack;
    $attack->setRootDir($_POST["dir"]);
    $attack->setMaxDepth($_POST["depth"]);
    $attack->saveState();
}