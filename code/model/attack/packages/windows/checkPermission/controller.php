<?php

if (isset($_POST["directories"])) {
    global $attack;
    $attack->setDirectories($_POST["directories"]);
    $attack->saveState();
}