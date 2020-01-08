<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Network;

use Kelvinho\Virus\Header;
use function Kelvinho\Virus\map;

class RequestData {
    public array $getVariables;
    private array $postVariables;
    public array $serverVariables;
    private array $sessionVariables;
    private array $explodedPath;
    private array $params;

    /** @noinspection PhpUnusedParameterInspection */
    public function __construct() {
        $this->getVariables = $_GET;
        $this->postVariables = $_POST;
        $this->serverVariables = $_SERVER;
        $this->sessionVariables = $_SESSION;
        $this->params = [];
        if (!strpos($this->serverVariables["REQUEST_URI"], "?")) {
            $this->explodedPath = explode("/", trim($this->serverVariables["REQUEST_URI"], "/"));
        } else {
            $this->explodedPath = explode("/", trim(explode("?", $this->serverVariables["REQUEST_URI"])[0], "/"));
            $params = explode("&", trim(explode("?", $this->serverVariables["REQUEST_URI"])[1], "/"));
            map($params, function ($value, $key, $params) {
                $contents = explode("=", $value);
                $params[$contents[0]] = $contents[1];
            }, $this->getVariables);
        }
    }

    public function getRequestMethod(): string {
        return $this->serverVariables["REQUEST_METHOD"];
    }

    public function get($key): ?string {
        return @$this->getVariables[$key];
    }

    public function hasGet($key): bool {
        return array_key_exists($key, $this->getVariables);
    }

    public function getCheck($key): string {
        if ($this->hasGet($key)) {
            return $this->get($key);
        } else {
            Header::badRequest();
            return null;
        }
    }

    public function post($key): ?string {
        return @$this->postVariables[$key];
    }

    public function hasPost($key): bool {
        return array_key_exists($key, $this->postVariables);
    }

    public function postCheck($key): string {
        if ($this->hasPost($key)) {
            return $this->post($key);
        } else {
            Header::badRequest();
            return null;
        }
    }

    public function getExplodedPath(): array {
        return $this->explodedPath;
    }

    public function getHost(): string {
        return "https://" . $this->serverVariables["HTTP_HOST"];
    }

    public function getParams(): array {
        return $this->params;
    }
}