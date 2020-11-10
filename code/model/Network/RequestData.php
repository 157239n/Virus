<?php

namespace Kelvinho\Virus\Network;

use Kelvinho\Virus\Singleton\Header;
use function Kelvinho\Virus\filter;

/**
 * Class RequestData. Wrapper for request data coming in and have convenience methods.
 *
 * @package Kelvinho\Virus\Network
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class RequestData {
    private array $getVariables;
    private array $postVariables;
    public array $serverVariables;
    private array $fileVariables;
    private array $explodedPath;
    private string $remoteIp = "93.184.216.34"; // ip address of example.com

    public function __construct() {
        $this->getVariables = $_GET;
        $this->postVariables = $_POST;
        $this->serverVariables = $_SERVER;
        $this->fileVariables = $_FILES;
        $this->explodedPath = array_values(filter(explode("/", $this->serverVariables["PHP_SELF"]), fn($el) => $el));
        if ($this->hasServer("HTTP_X_FORWARDED_FOR")) $this->remoteIp = explode(",", $this->serverCheck("HTTP_X_FORWARDED_FOR"))[0];
        else if ($this->hasServer("REMOTE_ADDR")) $this->remoteIp = $this->serverCheck("REMOTE_ADDR");
    }

    public function getRequestMethod(): string {
        return $this->serverVariables["REQUEST_METHOD"];
    }

    public function getRemoteIp(): string {
        return $this->remoteIp;
    }

    /**
     * If the GET variable doesn't exist, sets response code to bad request and exits.
     *
     * @param string $key
     * @return string
     */
    public function getCheck(string $key): string {
        return $this->hasGet($key) ? $this->get($key) : Header::badRequest();
    }

    /**
     * Whether a GET variable exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasGet(string $key): bool {
        return array_key_exists($key, $this->getVariables);
    }

    /**
     * Gets a GET variable. This also includes variables in the URL.
     *
     * @param string $key
     * @param string|null $default Default value if parameter is not found
     * @return string|null
     */
    public function get(string $key, string $default = null): ?string {
        return $this->hasGet($key) ? $this->getVariables[$key] : $default;
    }

    /**
     * If the POST variable doesn't exist, sets response code to bad request and exits.
     *
     * @param string $key
     * @return string
     */
    public function postCheck(string $key): string {
        return ($this->hasPost($key)) ? $this->post($key) : Header::badRequest();
    }

    /**
     * Whether a POST variable exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasPost(string $key): bool {
        return array_key_exists($key, $this->postVariables);
    }

    /**
     * Gets a POST variable.
     *
     * @param string $key
     * @param string|null $default Default value if parameter is not found
     * @return string|null
     */
    public function post(string $key, string $default = null): ?string {
        return $this->hasPost($key) ? @$this->postVariables[$key] : $default;
    }

    /**
     * Gets the path (/abc/def in /abc/def?param1=val1) as an exploded array.
     *
     * @return array
     */
    public function getExplodedPath(): array {
        return $this->explodedPath;
    }

    /**
     * Gets the original path.
     *
     * @return string
     */
    public function getPath(): string {
        return $this->serverVariables["PHP_SELF"];
    }

    /**
     * Make sure the domain registered using the environment variable is the same as the one detected.
     * This is to avoid alt sites like cloud.kelvinho.org to actually redirect to the main site. This is to avoid reverse engineering attempts on the alt site
     *
     * @return bool
     */
    public function rightHost(): bool {
        return str_replace("http://", "", str_replace("https://", "", DOMAIN)) == $this->serverVariables["HTTP_HOST"];
    }

    /**
     * If the file doesn't exist, sets response code to bad request and exits.
     *
     * @param string $fileName
     * @return string
     */
    public function fileCheck(string $fileName): string {
        if (!$this->hasFile($fileName)) Header::badRequest();
        return $this->readfile($fileName);
    }

    /**
     * Whether this file exists.
     *
     * @param string $fileName
     * @return bool
     */
    public function hasFile(string $fileName): bool {
        return isset($this->fileVariables[$fileName]);
    }

    /**
     * Reads the file contents as a string.
     *
     * @param string $fileName
     * @param string|null $default
     * @return string
     */
    public function readfile(string $fileName, string $default = null): string {
        return $this->hasFile($fileName) ? file_get_contents($this->fileVariables[$fileName]["tmp_name"]) : $default;
    }

    public function filePath(string $fileName): string {
        return $this->fileVariables[$fileName]["tmp_name"];
    }

    /**
     * Does not read the file contents to a variable. Just dumps the file out standard output.
     *
     * @param string $fileName
     */
    public function dumpFileContents(string $fileName): void {
        if (!$this->hasFile($fileName)) Header::badRequest();
        readfile($this->fileVariables[$fileName]["tmp_name"]);
    }

    /**
     * Moves file to a desired location
     *
     * @param string $fileName
     * @param string $destination
     */
    public function moveFile(string $fileName, string $destination) {
        if (!$this->hasFile($fileName)) Header::badRequest();
        rename($this->fileVariables[$fileName]["tmp_name"], $destination);
    }

    /**
     * If the $_SERVER variable doesn't exist, sets response code to bad request and exits.
     *
     * @param string $key
     * @return string
     */
    public function serverCheck(string $key): string {
        return $this->hasServer($key) ? $this->server($key) : Header::badRequest();
    }

    /**
     * Whether a $_SERVER variable exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasServer(string $key): bool {
        return array_key_exists($key, $this->serverVariables);
    }

    /**
     * Gets a $_SERVER variable.
     *
     * @param string $key
     * @param string|null $default Default value if parameter is not found
     * @return string|null
     */
    public function server(string $key, string $default = null): ?string {
        return $this->hasServer($key) ? $this->serverVariables[$key] : $default;
    }
}