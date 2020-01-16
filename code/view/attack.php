<?php

use Kelvinho\Virus\Singleton\Header;

if (!$session->has("virus_id")) Header::redirectToHome();
if (!$session->has("attack_id")) Header::redirectToHome();

$virus_id = $session->getCheck("virus_id");
$attack_id = $session->getCheck("attack_id");

if (!$authenticator->authorized($virus_id, $attack_id)) Header::redirectToHome();

$attack = $attackFactory->get($attack_id);
$attack->render();
