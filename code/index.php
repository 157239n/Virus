<?php

require_once __DIR__ . "/autoload.php";

if ("https://" . $_SERVER["HTTP_HOST"] == DOMAIN) {
    header("Location: " . DOMAIN_DASHBOARD);
} else {
    header("Location: http://www.google.com");
}
