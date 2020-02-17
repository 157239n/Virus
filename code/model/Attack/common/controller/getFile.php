<?php

/**
 * Gets a specific file belonging to this attack
 */

/** @var \Kelvinho\Virus\Attack\AttackBase $this */

use Kelvinho\Virus\Singleton\Header;
use function Kelvinho\Virus\goodPath;

$file = $this->requestData->getCheck("file");
$desiredName = $this->requestData->get("desiredName", "file");

$absPath = goodPath(DATA_FILE, "/attacks/" . $this->getAttackId() . "/$file");
if (!$absPath) Header::notFound();

\header("Content-type: " . mime_content_type($absPath));
\header("Content-Disposition: inline; filename=\"" . base64_decode($desiredName) . "\"");
readfile($absPath);
