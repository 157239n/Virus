<?php

use Kelvinho\Virus\Attack\Classes;

/** @var \Kelvinho\Virus\Attack\PackageRegistrar $packageRegistrar */

$packageRegistrar->iRegister("win.oneTime.ScanPartitions", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ScanPartitions\\ScanPartitions", "easy.ScanPartitions", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Scans for every available partitions on the target computer.");
