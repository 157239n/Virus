<?php

namespace Kelvinho\Virus\Auth;

use Kelvinho\Virus\Session\Session;
use mysqli;

/**
 * Class Authenticator. Handles all the authentication work.
 *
 * @package Kelvinho\Virus\Auth
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class AuthenticatorImp implements Authenticator {
    private Session $session;
    private mysqli $mysqli;

    public function __construct(Session $session, mysqli $mysqli) {
        $this->session = $session;
        $this->mysqli = $mysqli;
    }

    /**
     * See whether this user is authorized to access this virus and this attack.
     *
     * @param string $virus_id The virus id
     * @param string|null $attack_id The attack id, optional
     * @return bool Whether this user is authorized to access this virus and this attack
     */
    public function authorized(string $virus_id = null, string $attack_id = null) {
        if ($virus_id == null) return false;
        if (!$this->authenticated()) return false;
        $answer = $this->mysqli->query("select user_handle from viruses where virus_id = \"" . $this->mysqli->escape_string($virus_id) . "\"");
        if ($answer) {
            $row = $answer->fetch_assoc();
            $authorized = $row ? $row["user_handle"] === $this->session->get("user_handle") : false;
        } else $authorized = false;
        if ($attack_id != null) {
            $answer = $this->mysqli->query("select virus_id from attacks where attack_id = \"" . $this->mysqli->escape_string($attack_id) . "\"");
            if ($answer) {
                $row = $answer->fetch_assoc();
                $authorized = $row ? $row["virus_id"] === $virus_id : false;
            } else $authorized = false;
        }
        return $authorized;
    }

    /**
     * Returns whether the user is authenticated.
     *
     * @param string|null $user_handle Optional user handle to make sure that the authenticated user is the same as the requesting user
     * @return bool Whether the user is authenticated
     */
    public function authenticated(string $user_handle = null): bool {
        if (empty($user_handle)) {
            return $this->session->has("user_handle");
        } else {
            return $this->session->get("user_handle") === $user_handle;
        }
    }

    /**
     * Authenticates a user.
     *
     * @param string $user_handle The user's handle
     * @param string $password The user's password
     * @return bool Whether the user is authenticated
     */
    public function authenticate(string $user_handle, string $password): bool {
        $authenticated = false;
        $answer = $this->mysqli->query("select password_salt, password_hash from users where user_handle = \"" . $this->mysqli->escape_string($user_handle) . "\"");
        if ($answer)
            if ($row = $answer->fetch_assoc())
                if (hash("sha256", $row["password_salt"] . $password) == $row["password_hash"])
                    $authenticated = true;
        if ($authenticated) {
            $this->session->set("user_handle", $user_handle);
            return true;
        } else return false;
    }
}