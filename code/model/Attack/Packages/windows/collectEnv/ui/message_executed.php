<?php

use function Kelvinho\Virus\map;

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