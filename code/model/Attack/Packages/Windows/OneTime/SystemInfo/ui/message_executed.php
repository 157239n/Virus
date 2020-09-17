<?php

use function Kelvinho\Virus\map;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\SystemInfo\SystemInfo $attack */

?>
<p>Here are the systems information:</p>
<ul style="list-style-type: none;overflow: auto;">
    <?php map(explode("\n", $attack->getSystemInfo()), function ($line) { ?>
        <li>
            <pre><?php echo $line; ?></pre>
        </li>
    <?php }); ?>
</ul>
