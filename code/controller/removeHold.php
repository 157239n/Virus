<?php

use Kelvinho\Virus\Singleton\Header;

if (!$authenticator->authenticated()) Header::forbidden();

$user = $userFactory->get($session->get("user_handle"));
$user->removeHold();
$user->saveState();
