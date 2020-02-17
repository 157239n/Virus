<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.Power", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Power\\Power", "easy.Power", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Power-related operations: shutdown or restart");
