<?php

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\User\User;
use function Kelvinho\Virus\db;

$user_handle = $requestData->postCheck("user_handle");
$password = $requestData->postCheck("password");
$name = $requestData->postCheck("name");
$timezone = (int)$requestData->postCheck("timezone");

if (strlen($user_handle) > 20) Header::badRequest();
if (strlen($name) > 100) Header::badRequest();
if (preg_match('/[^A-Za-z0-9_]/', $user_handle)) Header::badRequest();
if (User::exists($user_handle)) Header::badRequest();

$userFactory->new($user_handle, $password, $name, $timezone);
