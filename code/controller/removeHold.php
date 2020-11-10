<?php

use Kelvinho\Virus\Singleton\Header;

global $authenticator, $userFactory, $session;

if (!$authenticator->authenticated()) Header::forbidden();
$userFactory->current()->removeHold()->saveState();
