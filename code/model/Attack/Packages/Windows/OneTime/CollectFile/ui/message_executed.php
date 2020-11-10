<?php

use Kelvinho\Virus\Singleton\Torch;
use function Kelvinho\Virus\map;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectFile\CollectFile $attack */

?>
<p>This attack is executed. Available files:</p>
<ul style="overflow: auto; background-color: var(--surface)">
    <?php
    $files = $attack->getFiles(false);
    if (count($files) === 0) echo "(No files)";
    else {
        map($files, function ($filePath, $index) { ?>
            <li>
                <pre style="cursor: pointer;"
                     onclick="openFile('<?php echo "file$index"; ?>', '<?php echo base64_encode(Torch::end(explode("\\", $filePath))); ?>')"><?php echo htmlspecialchars($filePath); ?></pre>
            </li>
        <?php });
    } ?>
</ul>
<p>These files are either nonexistent, truly empty, or is too big to transfer:</p>
<ul style="overflow: auto; background-color: var(--surface)">
    <?php echo (count($files = $attack->getFiles(true)) === 0) ? "(No files)" : implode(map($files, fn($fileName) => "<li><pre>" . htmlspecialchars($fileName) . "</pre></li>")); ?>
</ul>