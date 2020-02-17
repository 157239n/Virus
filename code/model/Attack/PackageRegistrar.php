<?php

namespace Kelvinho\Virus\Attack;

/**
 * Class PackageRegistrar, Singleton
 * Oversees the attack packages. They look like scanPartitions, but their real class name with namespace will be Kelvinho\Virus\Attack\Packages\ScanPartitions
 *
 * @package Kelvinho\Virus\Attack
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class PackageRegistrar {
    private array $iPackages = [];
    private array $iPackageNames = [];

    /**
     * Registers an attack package. This is supposed to be called from packages/{platform}/{package name}/register.php
     *
     * @param string $dbName Name stored in the database
     * @param string $className Actual class name to instantiate AttackBase object
     * @param string $displayName Name displayed to users
     * @param string $location Directory of the package
     * @param array $classes The groups this package belongs to
     * @param string $description Description for users
     */
    public function iRegister(string $dbName, string $className, string $displayName, string $location, array $classes, string $description) {
        $this->iPackages[$dbName] = ["className" => $className, "displayName" => $displayName, "location" => $location, "classes" => $classes, "description" => $description];
        array_push($this->iPackageNames, $dbName);
    }

    /**
     * Get array of packages db names
     *
     * @return array
     */
    public function getPackages(): array {
        return $this->iPackageNames;
    }

    /**
     * Whether a package is available
     *
     * @param string $dbName Name of package stored in the database
     * @return bool
     */
    public function hasPackage(string $dbName): bool {
        return isset($this->iPackages[$dbName]);
    }

    /**
     * Gets the package's display name
     *
     * @param string $dbName Name of package stored in the database
     * @return string The package display name
     */
    public function getDisplayName(string $dbName): string {
        return $this->iPackages[$dbName]["displayName"];
    }

    /**
     * Gets the package's description
     *
     * @param string $dbName The package name
     * @return string The package description
     */
    public function getDescription(string $dbName): string {
        return $this->iPackages[$dbName]["description"];
    }

    /**
     * Convert the package name (like scanPartitions) to the actual class name (like Kelvinho\Virus\Attack\Packages\ScanPartitions).
     * This is used for reflection and to create new attack objects easily.
     *
     * @param string $dbName The package name
     * @return string The actual class name
     */
    public function getClassName(string $dbName) {
        return $this->iPackages[$dbName]["className"];
    }

    /**
     * Gets the package's description. This is used for rendering the page
     *
     * @param string $dbName The package name
     * @return string The package description
     */
    public function getLocation(string $dbName) {
        return $this->iPackages[$dbName]["location"];
    }
}