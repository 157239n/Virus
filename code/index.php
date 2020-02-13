<?php

require_once(__DIR__ . "/autoload.php");

/** @var $router \Kelvinho\Virus\Network\Router */
$router->run();

/** @var $mysqli mysqli */
$mysqli->close();