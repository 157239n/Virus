<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.NewVirus", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\NewVirus\\NewVirus", "easy.NewVirus", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Install a new virus");
