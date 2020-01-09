<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Singleton;

use function Kelvinho\Virus\formattedTime;

/**
 * Class Logs, handles logging.
 *
 * @package Kelvinho\Virus\Singleton
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
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
     * Logs when a virus pings back but is not recognized.
     *
     * @param string $virus_id The virus id
     */
    public static function strayVirus(string $virus_id): void {
        file_put_contents(STRAY_VIRUS_LOG_FILE, "Virus $virus_id pings at " . time() . " (" . formattedTime() . ")\n", FILE_APPEND);
        Header::notFound();
    }

    /**
     * Logs when an attack pings back but is not recognized. Actually for now I'm just lazy implementing this
     *
     * @param string $attack_id
     */
    public static function strayAttack(string $attack_id): void {
        Header::forbidden();
        echo $attack_id;
    }

    /**
     * Logs an unreachable place, needs further debugging.
     *
     * @param string $where The general place where this is called, to locate the problem easier
     */
    public static function unreachableState(string $where): void {
        Logs::error("This is supposed to be unreachable. Where: $where");
    }
}