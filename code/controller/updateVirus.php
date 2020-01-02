<?php

require_once(__DIR__ . "/../autoload.php");

use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\Virus;
use function Kelvinho\Virus\checkVariable;

$virus_id = checkVariable($_POST["virus_id"]);
$name = checkVariable($_POST["name"]);
//$ping_interval = checkVariable($_POST["ping_interval"]);
$profile = checkVariable($_POST["profile"]);

if (!Virus::exists($virus_id)) {
    Header::badRequest();
}

if (!Authenticator::authorized($virus_id)) {
    Header::forbidden();
} else {
    $virus = Virus::get($virus_id);;
    $virus->setName($name);
    //$virus->setPingInterval((int)$ping_interval);
    //$virus->setPingInterval(7);
    $virus->setProfile($profile);
    $virus->saveState();
}