<?php

namespace Kelvinho\Virus\User;

use mysqli;

/**
 * Class UserFactoryImp
 *
 * @package Kelvinho\Virus\User
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class UserFactoryImp implements UserFactory {
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }

    public function new(string $user_handle, string $password, string $name, int $timezone = 0): User {
        $password_salt = substr(hash("sha256", rand()), 0, 5);
        $password_hash = hash("sha256", $password_salt . $password);

        mkdir(DATA_FILE . "/users/$user_handle");
        $this->mysqli->query("insert into users (user_handle, password_hash, password_salt, name, timezone, hold) values (\"$user_handle\", \"$password_hash\", \"$password_salt\", \"" . $this->mysqli->escape_string($name) . "\", $timezone, b'0')");

        return new User($user_handle, $this->mysqli);
    }

    public function get(string $user_handle): User {
        if (!$this->exists($user_handle)) throw new UserNotFound();
        return new User($user_handle, $this->mysqli);
    }

    public function exists(string $user_handle): bool {
        $answer = $this->mysqli->query("select user_handle from users where user_handle = \"" . $this->mysqli->escape_string($user_handle) . "\"");
        $hasHandle = false;
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                $hasHandle = true;
            }
        }
        return $hasHandle;
    }
}