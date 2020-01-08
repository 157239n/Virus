<?php

use Kelvinho\Virus\Header;

$user_handle = $requestData->post("user_handle");
$password = $requestData->post("password");

$authenticator->authenticate($user_handle, $password);
if ($authenticator->authenticated()) {
    Header::ok();
} else {
    Header::forbidden();
}
