<?php

namespace Kelvinho\Virus;

/**
 * Class User
 * @package Kelvinho\Virus
 *
 * Represents a user. The representation of this will be stored in table users only. No data is stored on disk.
 * But if needed in the future, it should be placed at DATA_FILE/users/{user_id}/
 */
class User {
    private string $user_handle;
    private string $name;
    private int $timezone = 0;

    private function fetchData(): void {
        if (Authenticator::authenticated($this->user_handle)) {
            $mysqli = db();
            if ($mysqli->connect_errno) {
                logMysql($mysqli->connect_error);
            }
            $answer = $mysqli->query("select name, timezone from users where user_handle = \"$this->user_handle\"");
            while ($row = $answer->fetch_assoc()) {
                $this->name = $row["name"];
                $this->timezone = $row["timezone"];
            }
            $mysqli->close();
        } else {
            logError("This shouldn't have happened, \\Kelvinho\\Virus\\User\\fetchData()");
        }
    }

    private function __construct(string $user_handle) {
        $this->user_handle = $user_handle;
        $this->fetchData();
    }

    /**
     * Deletes the user permanently.
     */
    public function delete(): void {
        map(User::getViruses($this->user_handle, Virus::VIRUS_ALL), function (/** @noinspection PhpUnusedParameterInspection */ $last_ping, $virus_id) {
            $virus = Virus::get($virus_id);
            $virus->delete();
        });
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $mysqli->query("delete from users where user_handle = \"$this->user_handle\"");
        $mysqli->close();
    }

    public function getTimezone(): int {
        return $this->timezone;
    }

    public function setTimezone(int $timezone): void {
        $this->timezone = $timezone;
    }

    public function saveState(): void {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $mysqli->query("update attacks set timezone = $this->timezone where user_handle = \"$this->user_handle\"");
        $mysqli->close();
    }

    /**
     * Get viruses as an associative array with attack_id => last_ping.
     *
     * @param string $user_handle
     * @param int $virusStatus
     * @return array Associative array with attack_id => last_ping
     */
    public static function getViruses(string $user_handle, int $virusStatus): array {
        switch ($virusStatus) {
            case Virus::VIRUS_ALL:
                $mysqli = db();
                if ($mysqli->connect_errno) {
                    logMysql($mysqli->connect_error);
                }
                $viruses = [];
                $answer = $mysqli->query("select virus_id, last_ping from viruses where user_handle = \"$user_handle\"");
                if ($answer) {
                    while ($row = $answer->fetch_assoc()) {
                        $viruses[$row["virus_id"]] = $row["last_ping"];
                    }
                }
                $mysqli->close();
                return $viruses;
            case Virus::VIRUS_ACTIVE:
                return filter(User::getViruses($user_handle, Virus::VIRUS_ALL), function (int $last_ping) {
                    return Virus::getState($last_ping) == Virus::VIRUS_ACTIVE;
                }, null, false);
            case Virus::VIRUS_DORMANT:
                return filter(User::getViruses($user_handle, Virus::VIRUS_ALL), function (int $last_ping) {
                    return Virus::getState($last_ping) == Virus::VIRUS_DORMANT;
                }, null, false);
            case Virus::VIRUS_LOST:
                return filter(User::getViruses($user_handle, Virus::VIRUS_ALL), function (int $last_ping) {
                    return Virus::getState($last_ping) == Virus::VIRUS_LOST;
                }, null, false);
            case Virus::VIRUS_EXPECTING:
                return filter(User::getViruses($user_handle, Virus::VIRUS_ALL), function (int $last_ping) {
                    return Virus::getState($last_ping) == Virus::VIRUS_EXPECTING;
                }, null, false);
            default:
                logError("Virus status of $virusStatus is not found");
                return null;
        }
    }

    /**
     * Creates a new user with a handle, a password and a name. Returns null if handle exists.
     *
     * @param string $user_handle User handle. Must be unique.
     * @param string $password Password
     * @param string $name Name
     * @param int $timezone
     * @return User The new user. Returns null if handle already exists
     */
    public static function new(string $user_handle, string $password, string $name, int $timezone = 0): User {
        if (self::exists($user_handle)) {
            return null;
        } else {
            // adding user to database
            $mysqli = db();
            if ($mysqli->connect_errno) {
                logMysql($mysqli->connect_error);
            }
            $mysqli->query("insert into users (user_handle, password, name, timezone) values (\"$user_handle\", \"$password\", \"" . $mysqli->escape_string($name) . "\", $timezone)");
            $mysqli->close();
            return new User($user_handle);
        }
    }

    /**
     * Checks whether a particular user handle exists.
     *
     * @param string $user_handle The user handle
     * @return bool Whether it exists
     */
    public static function exists(string $user_handle): bool {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select user_handle from users where user_handle = \"" . $mysqli->escape_string($user_handle) . "\"");
        $mysqli->close();
        $hasHandle = false;
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                $hasHandle = true;
            }
        }
        return $hasHandle;
    }

    /**
     * Get a user from a user handle. Returns null if not found
     *
     * @param string $user_handle The user handle
     * @return User|null
     */
    public static function get(string $user_handle): ?User {
        if (self::exists($user_handle)) {
            return new User($user_handle);
        } else {
            return null;
        }
    }
}
