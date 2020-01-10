<?php

namespace Kelvinho\Virus\Network;

use Kelvinho\Virus\Singleton\Header;

/**
 * Class Router. Contains multiple routes and will route to the correct location.
 *
 * @package Kelvinho\Virus\Network
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Router {
    private RequestData $requestData;
    private array $routes;

    /**
     * Router constructor.
     * @param RequestData $requestData
     */
    public function __construct(RequestData $requestData) {
        $this->requestData = $requestData;
    }

    /**
     * Create a new GET route.
     *
     * @param string $identifier
     * @param callable $callback
     */
    public function get(string $identifier, callable $callback) {
        $this->routes[] = new Route($identifier, "GET", $callback);
    }

    /**
     * Create multiple new GET routes with the same callback.
     *
     * @param array $identifiers
     * @param callable $callback
     */
    public function getMulti(array $identifiers, callable $callback) {
        foreach ($identifiers as $identifier) {
            $this->routes[] = new Route($identifier, "GET", $callback);
        }
    }

    /**
     * Create a new POST route.
     *
     * @param string $identifier
     * @param callable $callback
     */
    public function post(string $identifier, callable $callback) {
        $this->routes[] = new Route($identifier, "POST", $callback);
    }

    /**
     * Create multiple new POST routes with the same callback.
     *
     * @param array $identifiers
     * @param callable $callback
     */
    public function postMulti(array $identifiers, callable $callback) {
        foreach ($identifiers as $identifier) {
            $this->routes[] = new Route($identifier, "POST", $callback);
        }
    }

    /**
     * Look for the good route and execute it.
     */
    public function run(): void {
        foreach ($this->routes as $route) {
            /** @var Route $route */
            if (!$route->matches($this->requestData->getExplodedPath(), $this->requestData->getRequestMethod())) continue;
            $route->run();
            return;
        }
        Header::redirectToHome();
    }
}