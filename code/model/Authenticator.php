<?php


namespace Kelvinho\Virus;

session_start();

class Authenticator {
    /**
     * Returns whether the user is authenticated.
     *
     * @param string|null $user_handle Optional user handle to make sure that the authenticated user is the same as the requesting user
     * @return bool Whether the user is authenticated
     */
    public static function authenticated(string $user_handle = null): bool {
        if (empty($user_handle)) {
            return isset($_SESSION["user_handle"]);
        } else {
            if (isset($_SESSION["user_handle"])) {
                return $_SESSION["user_handle"] === $user_handle;
            } else {
                return false;
            }
        }
    }

    /**
     * See whether this user is authorized to access this virus and this attack.
     *
     * @param string $virus_id The virus id
     * @param string|null $attack_id The attack id, optional
     * @return bool Whether this user is authorized to access this virus and this attack
     */
    public static function authorized(string $virus_id, string $attack_id = null): bool {
        if (self::authenticated()) {
            $mysqli = db();
            if ($mysqli->connect_errno) {
                logMysql($mysqli->connect_error);
            }
            $answer = $mysqli->query("select user_handle from viruses where virus_id = \"$virus_id\"");
            if ($answer) {
                $row = $answer->fetch_assoc();
                if ($row) {
                    $authorized = $row["user_handle"] === $_SESSION["user_handle"];
                } else {
                    $authorized = false;
                }
            } else {
                $authorized = false;
            }
            if ($attack_id != null) {
                $answer = $mysqli->query("select virus_id from attacks where attack_id = \"$attack_id\"");
                if ($answer) {
                    $row = $answer->fetch_assoc();
                    if ($row) {
                        $authorized = $row["virus_id"] === $virus_id;
                    } else {
                        $authorized = false;
                    }
                } else {
                    $authorized = false;
                }
            }
            $mysqli->close();
            return $authorized;
        } else {
            return false;
        }
    }

    /**
     * Authenticates a user.
     *
     * @param string $user_handle The user's handle
     * @param string $password The user's password
     * @return bool Whether the user is authenticated
     */
    public static function authenticate(string $user_handle, string $password): bool {
        $authenticated = false;
        $mysqli = db();
        $answer = $mysqli->query("select password_salt, password_hash from users where user_handle = \"" . $mysqli->escape_string($user_handle) . "\"");
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                if (hash("sha256", $row["password_salt"] . $password) == $row["password_hash"]) {
                    $authenticated = true;
                }
            }
        }
        $mysqli->close();
        if ($authenticated) {
            $_SESSION["user_handle"] = $user_handle;
            return true;
        } else {
            return false;
        }
    }
}