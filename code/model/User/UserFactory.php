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
     * @param string $timezone
     * @return User The new user. Returns null if handle already exists
     */
    public function new(string $user_handle, string $password, string $name, string $timezone): User;

    /**
     * Get a user from a user handle. Returns null if not found
     *
     * @param string $user_handle The user handle
     * @return User|null
     */
    public function get(string $user_handle): User;

    /**
     * Checks whether a particular user handle exists.
     *
     * @param string $user_handle The user handle
     * @return bool Whether it exists
     */
    public function exists(string $user_handle): bool;

    /**
     * Get all user handles
     *
     * @return array
     */
    public function getAll(): array;
}
