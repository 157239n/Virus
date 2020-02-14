<?php


namespace Kelvinho\Virus\Network;

/**
 * Class Route. Represents a specific route to be handled by a callback.
 *
 * @package Kelvinho\Virus\Network
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Route {
    private array $fragments;
    private string $requestMethod;
    private $callback;

    /**
     * Route constructor.
     *
     * @param string $identifier Identifier for this route, should look something like vrs/*\/aks
     * @param string $requestMethod Request method, either GET or POST
     * @param callable $callback Callback function, will be called when it's the right path
     */
    public function __construct(string $identifier, string $requestMethod, callable $callback) {
        $this->fragments = explode("/", $identifier);
        $this->requestMethod = $requestMethod;
        $this->callback = $callback;
    }

    /**
     * Whether an existing path (aka incoming route) and request method matches this route.
     *
     * @param array $explodedPath
     * @param string $requestMethod
     * @return bool
     */
    public function matches(array $explodedPath, string $requestMethod) {
        if ($this->requestMethod != $requestMethod) return false;
        if (count($explodedPath) != count($this->fragments)) return false;
        for ($i = 0; $i < count($explodedPath); $i++) {
            if ($this->fragments[$i] == "*") continue;
            if ($this->fragments[$i] != $explodedPath[$i]) return false;
        }
        return true;
    }

    /**
     * Executes the callback.
     *
     * @return mixed the callback's return
     */
    public function run() {
        $callback = $this->callback;
        return $callback();
    }
}