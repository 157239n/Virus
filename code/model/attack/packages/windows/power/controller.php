<?php

use Kelvinho\Virus\Attack\AttackPackages\Power;

if (isset($_POST["type"])) {
    global $attack;
    switch (trim($_POST["type"])) {
        case "Shutdown":
            $attack->setType(Power::$POWER_SHUTDOWN);
            break;
        case "Restart":
            $attack->setType(Power::$POWER_RESTART);
            break;
        default:
            $attack->setType(Power::$POWER_RESTART);
    }
    $attack->saveState();
}