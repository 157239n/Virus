<?php

namespace Kelvinho\Virus\Attack;
/**
 * Class Classes
 *
 * @package Kelvinho\Virus\Attack
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Classes {
    /*
     * The values of these constants are the values that will be stored in the database, and used to transact everything
     *
     * How this works is that a package may declare a whole bunch of classes, but a virus can only declare one.
     *
     * TODO: This is only the primer to all of this, and is not really needed right now. I feel this current architecture is a bit flimsy too, so only implement this if I really go cross platform
     */
    public const WINDOWS_ALONE = "win_alone";
    public const WINDOWS_SWARM = "win_swarm";
    public const MAC_ALONE = "mac_alone";
    public const MAC_SWARM = "mac_swarm";

    private static array $classes = [self::WINDOWS_ALONE, self::WINDOWS_SWARM, self::MAC_ALONE, self::MAC_SWARM];

    public static function get(): array {
        return self::$classes;
    }

    public static function exists(string $class): bool {
        return in_array($class, self::$classes);
    }
}