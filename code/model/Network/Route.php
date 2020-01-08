<?php /** @noinspection PhpUnused */

/** @noinspection PhpMissingFieldTypeInspection */

namespace Kelvinho\Virus\Network;

class Route {
    private array $fragments;
    private string $requestMethod;
    private $callback;

    public function __construct(string $identifier, string $requestMethod, callable $callback) {
        $this->fragments = explode("/", $identifier);
        $this->requestMethod = $requestMethod;
        $this->callback = $callback;
    }

    public function matches(array $explodedPath, string $requestMethod) {
        if ($this->requestMethod != $requestMethod) return false;
        if (count($explodedPath) != count($this->fragments)) return false;
        for ($i = 0; $i < count($explodedPath); $i++) {
            if ($this->fragments[$i] == "*") continue;
            if ($this->fragments[$i] != $explodedPath[$i]) return false;
        }
        return true;
    }

    public function run() {
        $callback = $this->callback;
        return $callback();
    }
}