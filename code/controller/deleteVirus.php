<?php

use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\Virus;

require_once(__DIR__ . "/../autoload.php");

// TODO: may be before deleting the virus, send a signal to the virus to terminate itself, to stop turning it into a stray virus

if (!Virus::exists($_POST["virus_id"])) {
    Header::forbidden();
}
$virus_id = $_POST["virus_id"];

if (!Authenticator::authorized($virus_id)) {
    Header::forbidden();
} else {
    $_SESSION["virus_id"] = $virus_id;
    $virus = Virus::get($virus_id);
    $virus->delete();
    Header::ok();
}