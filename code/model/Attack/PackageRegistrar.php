<?php

namespace Kelvinho\Virus\Attack;

/**
 * Class PackageRegistrar, Singleton
 * @package Kelvinho\Virus\Attack
 *
 * Oversees the attack packages. They look like scanPartitions, but their real class name with namespace will be Kelvinho\Virus\Attack\Packages\ScanPartitions
 */
class PackageRegistrar {
    private static array $packages = [];
    private static array $packageNames = [];

    /**
     * Registers an attack package. This is supposed to be called from packages/{platform}/{package name}/register.php
     *
     * @param string $dbName Name stored in the database
     * @param string $className Actual class name to instantiate AttackInterface object
     * @param string $displayName Name displayed to users
     * @param string $location Directory of the package
     * @param array $classes The groups this package belongs to
     * @param string $description Description for users
     */
    public static function register(string $dbName, string $className, string $displayName, string $location, array $classes, string $description) {
        self::$packages[$dbName] = ["className" => $className, "displayName" => $displayName, "location" => $location, "classes" => $classes, "description" => $description];
        array_push(self::$packageNames, $dbName);
    }

    /**
     * Get array of packages db names
     *
     * @return array
     */
    public static function getPackages(): array {
        return self::$packageNames;
    }

    /**
     * Whether a package is available
     *
     * @param string $dbName Name of package stored in the database
     * @return bool
     */
    public static function hasPackage(string $dbName): bool {
        return isset(self::$packages[$dbName]);
    }

    /**
     * Gets the package's display name
     *
     * @param string $dbName Name of package stored in the database
     * @return string The package display name
     */
    public static function getDisplayName(string $dbName): string {
        return self::$packages[$dbName]["displayName"];
    }

    /**
     * Gets the package's description
     *
     * @param string $dbName The package name
     * @return string The package description
     */
    public static function getDescription(string $dbName): string {
        return self::$packages[$dbName]["description"];
    }

    /**
     * Convert the package name (like scanPartitions) to the actual class name (like Kelvinho\Virus\Attack\Packages\ScanPartitions).
     * This is used for reflection and to create new attack objects easily.
     *
     * @param string $dbName The package name
     * @return string The actual class name
     */
    public static function getClassName(string $dbName) {
        return self::$packages[$dbName]["className"];
    }

    /**
     * Gets the package's description. This is used for rendering the page
     *
     * @param string $dbName The package name
     * @return string The package description
     */
    public static function getLocation(string $dbName) {
        return self::$packages[$dbName]["location"];
    }
}