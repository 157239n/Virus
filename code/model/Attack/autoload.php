<?php /** @noinspection PhpIncludeInspection */
foreach (glob(__DIR__ . "/*.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/Packages/*/*/*/code.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/Packages/*/*/*/register.php") as $file) require_once($file);