<?php

use Kelvinho\Virus\Singleton\Header;

$user_handle = $requestData->postCheck("user_handle");
$password = $requestData->postCheck("password");
$name = $requestData->postCheck("name");
$timezoneString = $requestData->postCheck("timezone");
if (!$timezone->hasTimezone($timezoneString)) Header::badRequest();

if (strlen($user_handle) > 20) Header::badRequest();
if (strlen($name) > 100) Header::badRequest();
if (preg_match('/[^A-Za-z0-9_]/', $user_handle)) Header::badRequest();
if ($userFactory->exists($user_handle)) Header::badRequest();

$userFactory->new($user_handle, $password, $name, $timezoneString);
