<?php

namespace Kelvinho\Virus;

/**
 * Class Header, Singleton
 * @package Kelvinho\Virus
 *
 * This is a convenient class for setting the http response code and exiting the script.
 */
class Header {
    public static function ok() {
        http_response_code(200);
        exit(0);
    }

    public static function forbidden() {
        http_response_code(403);
        exit(1);
    }

    public static function badRequest() {
        http_response_code(400);
        exit(1);
    }

    public static function notFound() {
        http_response_code(404);
        exit(1);
    }

    public static function redirect() {
        http_response_code(302);
        exit(0);
    }
}