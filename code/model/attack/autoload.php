<?php /** @noinspection PhpIncludeInspection */
foreach (glob(__DIR__ . "/*.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/packages/*/*/code.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/packages/*/*/register.php") as $file) require_once($file);