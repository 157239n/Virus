<?php

namespace Kelvinho\Virus;

class Session {
    public function get(string $key): string {
        return @$_SESSION[$key];
    }

    public function set(string $key, string $value): void {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool {
        return array_key_exists($key, $_SESSION);
    }

    public function getCheck(string $key): string {
        if ($this->has($key)) {
            return $this->get($key);
        } else {
            Header::badRequest();
            return null;
        }
    }
}