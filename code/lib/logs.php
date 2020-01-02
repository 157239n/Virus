<?php

namespace Kelvinho\Virus {
    /**
     * Logs stuff.
     *
     * @param string $message The message to log
     */
    function log(string $message): void {
        file_put_contents(LOG_FILE, $message . "\n", FILE_APPEND);
    }

    /**
     * Logs with "Error: " in front and exits the script.
     *
     * @param string $message The message to log
     */
    function logError(string $message): void {
        log("Error: " . $message);
        Header::badRequest();
    }

    /**
     * Logs when an endpoint doesn't exist.
     *
     * @param string $endpoint The endpoint
     */
    function logEndpoint(string $endpoint): void {
        logError("Endpoint $endpoint doesn't exist");
    }

    /**
     * Logs when mysql failed.
     *
     * @param string $message Some additional message
     */
    function logMysql(string $message): void {
        logError("Mysql failed. Info: $message");
    }

    /**
     * Logs when a class doesn't exist.
     *
     * @param string $classname The class name
     */
    function logClass(string $classname): void {
        logError("Class name $classname does not exist");
    }

    /**
     * Logs when a virus pings back but is not recognized.
     *
     * @param string $virus_id The virus id
     */
    function logStrayVirus(string $virus_id): void {
        file_put_contents(STRAY_VIRUS_LOG_FILE, "Virus $virus_id pings at " . time() . " (" . formattedTime() . ")\n", FILE_APPEND);
        Header::notFound();
    }

    /**
     * Logs an unrecognized attack status.
     *
     * @param string $status The status
     */
    function logAttackStatus(string $status): void {
        logError("Attack status of $status is not defined. This really should not happen at all and please dig into it immediately.");
    }

    /**
     * Logs an unreachable place, needs further debugging.
     *
     * @param string $where The general place where this is called, to locate the problem easier
     */
    function logUnreachable(string $where): void {
        logError("This is supposed to be unreachable. Where: $where");
    }
}