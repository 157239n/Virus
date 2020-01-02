<?php

require_once(__DIR__ . "/../autoload.php");

use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Header;

$user_handle = $_POST["user_handle"];
$password = $_POST["password"];

Authenticator::authenticate($user_handle, $password);
if (Authenticator::authenticated()) {
    Header::ok();
} else {
    Header::forbidden();
}