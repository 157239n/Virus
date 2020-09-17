<?php

use Kelvinho\Virus\Singleton\Header;

global $authenticator, $userFactory, $session;

if (!$authenticator->authenticated()) Header::forbidden();

$user = $userFactory->get($session->get("user_handle"));
$user->removeHold();
$user->saveState();
