<?php

require_once(__DIR__ . "/../autoload.php");

use Kelvinho\Virus\Header;
use Kelvinho\Virus\User;
use function Kelvinho\Virus\db;

$user_handle = $_POST["user_handle"];
$password = $_POST["password"];
$name = $_POST["name"];
$timezone = (int)$_POST["timezone"];
$password_salt = substr(hash("sha256", rand()), 0, 5);
$password_hash = hash("sha256", $password_salt . $password);

if (strlen($user_handle) > 20) {
    Header::badRequest();
}

if (strlen($name) > 100) {
    Header::badRequest();
}

if (preg_match('/[^A-Za-z0-9_]/', $user_handle)) {
    Header::badRequest();
}

if (User::exists($user_handle)) {
    Header::badRequest();
} else {
    mkdir(DATA_FILE . "/users/$user_handle");
    $mysqli = db();
    $mysqli->query("insert into users (user_handle, password_hash, password_salt, name, timezone) values (\"$user_handle\", \"$password_hash\", \"$password_salt\", \"" . $mysqli->escape_string($name) . "\", $timezone)");
    $mysqli->close();
    Header::ok();
}