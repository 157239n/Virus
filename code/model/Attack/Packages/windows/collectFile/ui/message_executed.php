<?php

use function Kelvinho\Virus\map;

?>
<p>This attack is executed. Available files:</p>
<ul style="overflow: auto;">
    <?php
    $files = $attack->getNonEmptyFiles();
    if (count($files) === 0) {
        ?>(No files)<?php
    } else {
        map($files, function ($fileName, $index) { ?>
            <li>
                <pre style="cursor: pointer;"
                     onclick="openFile('<?php echo "file$index"; ?>', '<?php echo end(explode("\\", $fileName)); ?>')"><?php echo htmlspecialchars($fileName); ?></pre>
            </li>
        <?php });
    } ?>
</ul>
<p>These files are either nonexistent, truly empty, or is too big to transfer:</p>
<ul style="overflow: auto;">
    <?php
    $files = $attack->getEmptyFiles();
    if (count($files) === 0) {
        ?>(No files)<?php
    } else {
        map($files, function ($fileName) { ?>
            <li>
                <pre><?php echo htmlspecialchars($fileName); ?></pre>
            </li>
        <?php });
    } ?>
</ul>