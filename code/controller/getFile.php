<?php

// sends this a get parameter "file" with the name of the file inside of each attack's directory. This will check for
// permissions and then return back the file.

use Kelvinho\Virus\Singleton\Header;
use function Kelvinho\Virus\goodPath;

$file = $requestData->getCheck("file");
$desiredName = $requestData->get("desiredName", "file");

$virus_id = $session->getCheck("virus_id");
$attack_id = $session->getCheck("attack_id");

if (!$authenticator->authorized($virus_id, $attack_id)) Header::forbidden();

$relativePath = "/attacks/$attack_id/$file";
$absPath = DATA_FILE . $relativePath;

if (goodPath(DATA_FILE, $relativePath)) {
    \header("Content-type: " . mime_content_type($absPath));
    \header("Content-Disposition: inline; filename=\"$desiredName\"");
    readfile($absPath);
}
