<?php

namespace Kelvinho\Virus\Session;

use Kelvinho\Virus\Singleton\Header;

/**
 * Class Session, wrapper for all things session.
 *
 * @package Kelvinho\Virus
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Session {
    /**
     * Gets session variable.
     *
     * @param string $key
     * @param null $default Default value if session variable is not there
     * @return string|null
     */
    public function get(string $key, $default = null): ?string {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }

    /**
     * Sets session variable.
     *
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Whether session variable is there.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Get the session variable. Set response code to not found.
     *
     * @param string $key
     * @return string
     */
    public function getCheck(string $key): string {
        if ($this->has($key)) return $this->get($key);
        Header::notFound();
        return null;
    }
}