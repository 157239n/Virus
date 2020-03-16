<?php /** @noinspection PhpIncludeInspection */

namespace Kelvinho\Virus\Core;

/**
 * Class Autoload. PSR-0 compliant autoloader
 *
 * @package Kelvinho\Virus\Core
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Autoload {
    private string $modelPath;

    /**
     * Autoload constructor. Location of the model directory.
     * @param string $modelPath
     */
    public function __construct(string $modelPath) {
        $this->modelPath = rtrim($modelPath, "/");
    }

    /**
     * Attempts to load the class.
     *
     * @param string $className The class name
     * @return bool Whether it was successful
     */
    public function load(string $className): bool {
        $parts = explode("\\", trim($className, "\\/ "));
        if ($parts[0] !== "Kelvinho" || $parts[1] !== "Virus") return false;
        array_shift($parts);
        array_shift($parts);
        $path = $this->modelPath . "/" . implode("/", $parts) . ".php";
        if (!file_exists($path)) return false;
        require($path);
        return true;
    }

    /**
     * Registers the autoloader to PHP.
     *
     * @return bool Status of registration
     */
    public function register(): bool {
        return spl_autoload_register([$this, 'load']);
    }

    /**
     * Unregisters the autoloader to PHP.
     *
     * @return bool Status of registration
     */
    public function unregister(): bool {
        return spl_autoload_unregister([$this, 'load']);
    }
}
