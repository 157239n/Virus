<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Singleton;

use mysqli;
use RuntimeException;
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
     * Logs when a virus pings back but is not recognized.
     *
     * @param string $virus_id The virus id
     * @return int Dummy response
     */
    public static function strayVirus(string $virus_id): int {
        file_put_contents(DATA_DIR . "/logs/strayViruses", "Virus $virus_id pings at " . time() . " (" . formattedTime() . ")\n", FILE_APPEND);
        Header::notFound();
        return 0;
    }

    /**
     * Logs when an attack pings back but is not recognized. Actually for now I'm just lazy implementing this
     *
     * @param string $attack_id
     * @return int Dummy response
     */
    public static function strayAttack(string $attack_id): int {
        Header::forbidden();
        echo $attack_id;
        return 0;
    }

    /**
     * Logs when a path is not recognized.
     *
     * @param string $path
     * @return int Dummy response
     */
    public static function strayPath(string $path): int {
        file_put_contents(DATA_DIR . "/logs/strayPaths", "\n$path", FILE_APPEND);
        return 0;
    }

    /**
     * Logs an unreachable place, needs further debugging.
     *
     * @param string $where The general place where this is called, to locate the problem easier
     */
    public static function unreachableState(string $where): void {
        Logs::error("This is supposed to be unreachable. Where: $where");
    }

    /**
     * Logs with "Error: " in front and exits the script.
     *
     * @param string $message The message to log
     */
    public static function error(string $message): void {
        throw new RuntimeException("Error: " . $message);
    }

    /**
     * Logs stuff.
     *
     * @param string $message The message to log
     */
    public static function log(string $message): void {
        file_put_contents("/var/log/apache2/error.log", $message . "\n", FILE_APPEND);
    }

    public static function mysql(mysqli $mysqli): void {
        Logs::error("Mysql failed. Error: $mysqli->error");
    }

    public static function dump($object): void {
        ob_start();
        var_dump($object);
        Logs::log(ob_get_clean());
    }
}