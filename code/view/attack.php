<?php

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;

require_once(__DIR__ . "/../autoload.php");

if ($requestData->hasGet("attack_id")) {
    $attack_id = $requestData->get("attack_id");
    $session->set("attack_id", $attack_id);
} else {
    if ($session->has("attack_id")) {
        $attack_id = $session->get("attack_id");
    } else {
        \header("Location: " . DOMAIN);
        Header::redirect();
    }
}

$virus_id = $session->get("virus_id");

if (!AttackInterface::exists($attack_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}

if (!$authenticator->authorized($virus_id, $attack_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}

// above is just a bunch of security checks. If the user does not have permission or something is wrong then redirect them somewhere else

$attack = $attackFactory->get($attack_id);
$attack->render();
