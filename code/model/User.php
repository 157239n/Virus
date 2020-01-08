<?php

namespace Kelvinho\Virus;

use Kelvinho\Virus\Virus\Virus;

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
        $mysqli = db();
        if ($mysqli->connect_errno) {
            Logs::mysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select name, timezone from users where user_handle = \"$this->user_handle\"");
        while ($row = $answer->fetch_assoc()) {
            $this->name = $row["name"];
            $this->timezone = $row["timezone"];
        }
        $mysqli->close();
    }

    private function __construct(string $user_handle) {
        $this->user_handle = $user_handle;
        $this->fetchData();
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
            Logs::mysql($mysqli->connect_error);
        }
        $mysqli->query("update attacks set timezone = $this->timezone where user_handle = \"$this->user_handle\"");
        $mysqli->close();
    }

    /**
     * Get array of index -> {"virus_id" -> "{virus_id}", "last_ping" -> "{last_ping}"}
     *
     * @param string $user_handle
     * @param int $virusStatus
     * @return array
     */
    public static function getViruses(string $user_handle, int $virusStatus): array {
        switch ($virusStatus) {
            case Virus::VIRUS_ALL:
                $mysqli = db();
                if ($mysqli->connect_errno) {
                    Logs::mysql($mysqli->connect_error);
                }
                $viruses = [];
                $answer = $mysqli->query("select virus_id, last_ping from viruses where user_handle = \"$user_handle\"");
                if ($answer) {
                    while ($row = $answer->fetch_assoc()) {
                        array_push($viruses, array("virus_id" => $row["virus_id"], "last_ping" => $row["last_ping"]));
                    }
                }
                $mysqli->close();
                return $viruses;
            default:
                return filter(User::getViruses($user_handle, Virus::VIRUS_ALL), function ($data, /** @noinspection PhpUnusedParameterInspection */ $key, int $virusStatus) {
                    return Virus::getState($data["last_ping"]) == $virusStatus;
                }, $virusStatus);
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
                Logs::mysql($mysqli->connect_error);
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
            Logs::mysql($mysqli->connect_error);
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
/*
    public static function currentUserHandle(): ?string {
        return self::$current_user_handle;
    }
*/
}
