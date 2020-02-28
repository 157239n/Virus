<?php

use Kelvinho\Virus\Core\Autoload;

require_once(__DIR__ . "/../model/Core/Autoload.php");
$autoload = new Autoload(__DIR__ . "/../model");
$autoload->register();
