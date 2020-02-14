<?php

use Kelvinho\Virus\Network\Router;

require_once(__DIR__ . "/autoload.php");

/** @var $router Router */
$router->run();

/** @var $mysqli mysqli */
$mysqli->close();