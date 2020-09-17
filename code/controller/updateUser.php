<?php

use Kelvinho\Virus\Singleton\Header;

global $authenticator, $session, $userFactory, $timezone, $requestData;

if (!$authenticator->authenticated()) Header::forbidden();
$user = $userFactory->get($session->getCheck("user_handle"));
$user->setName($requestData->postCheck("name"));
$timezoneString = $requestData->postCheck("timezone");
$theme = $requestData->postCheck("theme") === "0" ? false : true;
if (!$timezone->hasTimezone($timezoneString)) Header::badRequest();
$user->setTimezone($timezoneString);
$user->setTheme($theme);
$user->saveState();
