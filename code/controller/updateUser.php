<?php

use Kelvinho\Virus\Singleton\Header;

global $authenticator, $session, $userFactory, $timezone, $requestData;

if (!$authenticator->authenticated()) Header::forbidden();
if (!$timezone->hasTimezone($timezoneString = $requestData->postCheck("timezone"))) Header::badRequest();
$theme = $requestData->postCheck("theme") === "0" ? false : true;
$userFactory->currentChecked()->setName($requestData->postCheck("name"))->setTimezone($timezoneString)->setTheme($theme)->saveState();
