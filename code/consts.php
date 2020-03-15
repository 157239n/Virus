<?php

define("GITHUB_PAGE", "https://github.com/157239n/Virus");

// logging and persistent states
define("LOG_FILE", "/var/log/apache2/error.log");
define("STRAY_VIRUS_LOG_FILE", "/var/log/apache2/strayViruses.log");
define("DATA_FILE", "/data"); // where all of the persistent state outside of the database lives

// domain related
define("DOMAIN", getenv("DOMAIN"));
define("ALT_DOMAIN", getenv("ALT_DOMAIN")); // this is to avoid "virus. ..." displayed to the target user when social engineering
define("ALT_SECURE_DOMAIN", getenv("ALT_SECURE_DOMAIN")); // this is to avoid "virus. ..." displayed to the target user when social engineering
define("ALT_DOMAIN_SHORT", str_replace("http://", "", ALT_DOMAIN));
define("DOMAIN_CONTROLLER", DOMAIN . "/controller");

// inner workings related
define("NAME_LENGTH_LIMIT", 20);

define("MAINTENANCE", false);

// all in seconds, original plan commented on the right
define("VIRUS_PING_INTERVAL", 7); // 10 minutes
define("ATTACK_UPLOAD_RETRY_INTERVAL", 5); // 1 minute
define("ATTACK_UPLOAD_RETRIES", 10);
define("ATTACK_RESOURCE_DOWNLOAD_RETRIES", 10);
define("SWARM_CLOCK_SPEED", 2); // thus, the checking time will be 2 times this
define("SWARM_CHECK_MULTIPLIER", 2); // liveliness check will have a window of time of SWARM_CLOCK_SPEED * SWARM_CHECK_MULTIPLIER seconds to talk to each other
define("SWARM_CREATION_MULTIPLIER", 5); // creation events will have a spare time to get things up to speed in SWARM_CLOCK_SPEED * SWARM_CREATION_MULTIPLIER before it's deemed failed and worth another try


