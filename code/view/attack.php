<?php

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;

require_once(__DIR__ . "/../autoload.php");

if (isset($_GET["attack_id"])) {
    $attack_id = $_GET["attack_id"];
    $_SESSION["attack_id"] = $attack_id;
} else {
    if (isset($_SESSION["attack_id"])) {
        $attack_id = $_SESSION["attack_id"];
    } else {
        header("Location: " . DOMAIN);
        Header::redirect();
    }
}

if (!AttackInterface::exists($attack_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}

$attack = AttackInterface::get($attack_id);
$attack->includeAdminPage();
