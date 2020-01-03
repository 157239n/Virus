<?php /** @noinspection PhpIncludeInspection */

foreach (glob(__DIR__ . "/exceptions/*.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/lib/*.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/model/*.php") as $file) require_once($file);
require_once(__DIR__ . "/model/attack/autoload.php");
require_once(__DIR__ . "/controller/Helper.php");
