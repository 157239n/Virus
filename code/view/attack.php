<?php

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Header;

require_once(__DIR__ . "/../autoload.php");

if (isset($_GET["attack_id"])) {
    $attack_id = $_GET["attack_id"];
    $_SESSION["attack_id"] = $attack_id;
} else {
    if (isset($_SESSION["attack_id"])) {
        $attack_id = $_SESSION["attack_id"];
    } else {
        \header("Location: " . DOMAIN);
        Header::redirect();
    }
}
if (isset($_SESSION["virus_id"])) {
    $virus_id = $_SESSION["virus_id"];
} else {
    \header("Location: " . DOMAIN);
    Header::redirect();
}

if (!Authenticator::authorized($virus_id, $attack_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}

if (!AttackInterface::exists($attack_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}

// above is just a bunch of security checks. If the user does not have permission or something is wrong then redirect them somewhere else

$attack = AttackInterface::get($attack_id);
$attack->includeAdminPage();
