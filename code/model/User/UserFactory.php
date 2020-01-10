<?php

namespace Kelvinho\Virus\User;

/**
 * Interface UserFactory. Responsible for getting and creating users
 *
 * @package Kelvinho\Virus\User
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
interface UserFactory {
    /**
     * Creates a new user with a handle, a password and a name. Returns null if handle exists.
     *
     * @param string $user_handle User handle. Must be unique.
     * @param string $password Password
     * @param string $name Name
     * @param int $timezone
     * @return User The new user. Returns null if handle already exists
     */
    public function new(string $user_handle, string $password, string $name, int $timezone = 0): User;

    /**
     * Get a user from a user handle. Returns null if not found
     *
     * @param string $user_handle The user handle
     * @return User|null
     */
    public function get(string $user_handle): User;
}