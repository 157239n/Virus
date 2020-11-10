<?php

namespace Kelvinho\Virus\Singleton;

/**
 * Class Header. Convenience class for setting http response code and exiting.
 *
 * @package Kelvinho\Virus\Singleton
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Header {
    public static function ok(): int {
        http_response_code(200);
        exit(0);
    }

    public static function forbidden(): int {
        http_response_code(403);
        exit(1);
    }

    public static function badRequest(): int {
        http_response_code(400);
        exit(1);
    }

    public static function notFound(): int {
        http_response_code(404);
        exit(1);
    }

    public static function redirectToHome(): int {
        header("Location: " . DOMAIN);
        return self::redirect();
    }

    public static function redirect(): int {
        http_response_code(302);
        exit(0);
    }
}