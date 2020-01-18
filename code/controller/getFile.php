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

$absPath = goodPath(DATA_FILE, "/attacks/$attack_id/$file");

if ($absPath) {
    \header("Content-type: " . mime_content_type($absPath));
    \header("Content-Disposition: inline; filename=\"$desiredName\"");
    readfile($absPath);
} else Header::notFound();
