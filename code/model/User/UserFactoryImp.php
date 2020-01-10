<?php

namespace Kelvinho\Virus\User;

use function Kelvinho\Virus\db;

/**
 * Class UserFactoryImp
 *
 * @package Kelvinho\Virus\User
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class UserFactoryImp implements UserFactory {
    public function new(string $user_handle, string $password, string $name, int $timezone = 0): User {
        $password_salt = substr(hash("sha256", rand()), 0, 5);
        $password_hash = hash("sha256", $password_salt . $password);

        mkdir(DATA_FILE . "/users/$user_handle");
        $mysqli = db();
        $mysqli->query("insert into users (user_handle, password_hash, password_salt, name, timezone, hold) values (\"$user_handle\", \"$password_hash\", \"$password_salt\", \"" . $mysqli->escape_string($name) . "\", $timezone, b'0')");
        $mysqli->close();

        return new User($user_handle);
    }

    public function get(string $user_handle): User {
        if (!User::exists($user_handle)) throw new UserNotFound();
        return new User($user_handle);
    }
}