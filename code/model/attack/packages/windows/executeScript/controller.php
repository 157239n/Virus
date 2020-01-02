<?php

if (isset($_POST["script"])) {
    global $attack;
    $attack->setScript($_POST["script"]);
    $attack->saveState();
}