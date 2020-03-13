<?php

use Kelvinho\Virus\Singleton\Header;

if (!$authenticator->authenticated()) Header::forbidden();
$user = $userFactory->get($session->getCheck("user_handle"));
$user->setName($requestData->postCheck("name"));
$user->setTimezone($requestData->postCheck("timezone"));
$user->saveState();
