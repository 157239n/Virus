<?php

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Controller\Helper;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\checkVariable;

require_once(__DIR__ . "/../autoload.php");

Helper::verifyIds($_POST["virus_id"], $_POST["attack_id"]);
$virus_id = $_POST["virus_id"];
$attack_id = $_POST["attack_id"];

$name = checkVariable($_POST["name"]);
$profile = checkVariable($_POST["profile"]);

if (!Authenticator::authorized($virus_id, $attack_id)) {
    Header::forbidden();
} else {
    $_SESSION["virus_id"] = $virus_id;
    $_SESSION["attack_id"] = $attack_id;
    $attack = AttackInterface::get($attack_id);
    $attack->setName($name);
    $attack->setProfile($profile);
    $attack->saveState();
    $attack->includeController();
}