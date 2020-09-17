<?php

namespace Kelvinho\Virus\Auth;

/**
 * Interface Authenticator. Handles all the authentication work.
 *
 * @package Kelvinho\Virus\Auth
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
interface Authenticator {
    /**
     * Returns whether the user is authenticated.
     *
     * @param string|null $user_handle Optional user handle to make sure that the authenticated user is the same as the requesting user
     * @return bool Whether the user is authenticated
     */
    public function authenticated(string $user_handle = null): bool;

    /**
     * See whether this user is authorized to access this virus and this attack.
     *
     * @param string|null $virus_id The virus id
     * @param string|null $attack_id The attack id, optional
     * @return bool Whether this user is authorized to access this virus and this attack
     */
    public function authorized(string $virus_id = null, string $attack_id = null);

    /**
     * Authenticates a user.
     *
     * @param string $user_handle The user's handle
     * @param string $password The user's password
     * @return bool Whether the user is authenticated
     */
    public function authenticate(string $user_handle, string $password): bool;
}