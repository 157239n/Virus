<?php

use Kelvinho\Virus\Singleton\Header;

global $requestData, $timezone, $userFactory;

$user_handle = $requestData->postCheck("user_handle");
$password = $requestData->postCheck("password");
$name = $requestData->postCheck("name");
if (!$timezone->hasTimezone($timezoneString = $requestData->postCheck("timezone"))) Header::badRequest();

if (strlen($user_handle) > 20) Header::badRequest();
if (strlen($name) > 100) Header::badRequest();
if (preg_match('/[^A-Za-z0-9_]/', $user_handle)) Header::badRequest();
if ($userFactory->exists($user_handle)) Header::badRequest();

$userFactory->new($user_handle, $password, $name, $timezoneString);
