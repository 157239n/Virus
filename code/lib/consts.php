<?php

define("GITHUB_PAGE", "https://github.com"); // TODO: publish this to Github. Remember to tuck away the secrets

// logging and persistent states
define("LOG_FILE", "/var/log/apache2/virusError.log"); // where should I log things to? Btw, there's the log and dellog utility bundled with this app
define("STRAY_VIRUS_LOG_FILE", "/var/log/apache2/strayViruses.log"); // where I should log unrecognized viruses when they pings back?
define("DATA_FILE", "/data"); // where all of the persistent state outside of a database lives

// domain related
#define("ALT_DOMAIN", "http://cloud.kelvinho.org"); // this is to avoid "virus. ..." displayed to the target user when social engineering
define("ALT_DOMAIN", getenv("ALT_DOMAIN"));
#define("ALT_SECURE_DOMAIN", "https://cloud.kelvinho.org"); // this is to avoid "virus. ..." displayed to the target user when social engineering
define("ALT_SECURE_DOMAIN", getenv("ALT_SECURE_DOMAIN")); // this is to avoid "virus. ..." displayed to the target user when social engineering
define("RESOURCE_DOMAIN", "http://resource.kelvinho.org"); // this is for static files, to be served by nginx. Currently not in use tho
define("DOMAIN_CONTROLLER", DOMAIN . "/controller");
define("DOMAIN_USER", DOMAIN . "/user");
define("DOMAIN_DASHBOARD", DOMAIN_USER . "/dashboard.php");
define("DOMAIN_VIRUS_INFO", DOMAIN_USER . "/virus.php");
define("DOMAIN_ATTACK_INFO", DOMAIN_USER . "/attack.php");

// internal locations related
define("PROJECT_ROOT", __DIR__ . "/..");
define("PROJECT_CONTROLLER", PROJECT_ROOT . "/controller");

// inner workings related
define("NAME_LENGTH_LIMIT", 20);

// all in seconds, original plan commented on the right
define("VIRUS_PING_INTERVAL", 7); // 10 minutes
define("STARTUP_DELAY", 3); // 5 minutes
define("ATTACK_UPLOAD_RETRY_INTERVAL", 5); // 1 minute
define("HIDDEN_PING_INTERVAL", 3); // thus, the checking time will be 2 times this

