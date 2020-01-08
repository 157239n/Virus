<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus;

/**
 * Class Logs, handles logging, Singleton
 *
 * @package Kelvinho\Virus
 */
class Logs {
    /**
     * Logs stuff.
     *
     * @param string $message The message to log
     */
    public static function log(string $message): void {
        file_put_contents(LOG_FILE, $message . "\n", FILE_APPEND);
    }

    /**
     * Logs with "Error: " in front and exits the script.
     *
     * @param string $message The message to log
     */
    public static function error(string $message): void {
        Logs::log("Error: " . $message);
        Header::badRequest();
    }

    /**
     * Logs when an endpoint doesn't exist.
     *
     * @param string $endpoint The endpoint
     */
    public static function endpoint(string $endpoint): void {
        Logs::error("Endpoint $endpoint doesn't exist");
    }

    /**
     * Logs when mysql failed.
     *
     * @param string $message Some additional message
     */
    public static function mysql(string $message): void {
        Logs::error("Mysql failed. Info: $message");
    }

    /**
     * Logs when a class doesn't exist.
     *
     * @param string $classname The class name
     */
    public static function class(string $classname): void {
        Logs::error("Class name $classname does not exist");
    }

    /**
     * Logs when a virus pings back but is not recognized.
     *
     * @param string $virus_id The virus id
     */
    public static function strayVirus(string $virus_id): void {
        file_put_contents(STRAY_VIRUS_LOG_FILE, "Virus $virus_id pings at " . time() . " (" . formattedTime() . ")\n", FILE_APPEND);
        Header::notFound();
    }

    /**
     * Logs an unrecognized attack status.
     *
     * @param string $status The status
     */
    public static function attackStatus(string $status): void {
        Logs::error("Attack status of $status is not defined. This really should not happen at all and please dig into it immediately.");
    }

    /**
     * Logs an unreachable place, needs further debugging.
     *
     * @param string $where The general place where this is called, to locate the problem easier
     */
    public static function unreachable(string $where): void {
        Logs::error("This is supposed to be unreachable. Where: $where");
    }
}