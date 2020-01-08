<?php

namespace Kelvinho\Virus\Network;

use Kelvinho\Virus\Header;

class Router {
    private RequestData $requestData;
    private array $routes;

    public function __construct(RequestData $requestData) {
        $this->requestData = $requestData;
    }

    public function get(string $identifier, callable $callback) {
        $this->routes[] = new Route($identifier, "GET", $callback);
    }

    public function getMulti(array $identifiers, callable $callback) {
        foreach ($identifiers as $identifier) {
            $this->routes[] = new Route($identifier, "GET", $callback);
        }
    }

    public function post(string $identifier, callable $callback) {
        $this->routes[] = new Route($identifier, "POST", $callback);
    }

    public function postMulti(array $identifiers, callable $callback) {
        foreach ($identifiers as $identifier) {
            $this->routes[] = new Route($identifier, "POST", $callback);
        }
    }

    public function run() {
        foreach ($this->routes as $route) {
            if (!$route->matches($this->requestData->getExplodedPath(), $this->requestData->getRequestMethod())) continue;
            $route->run();
            return;
        }
        header("Location: " . DOMAIN);
        Header::redirect();
    }
}