<?php

namespace Kelvinho\Virus\User;

use Kelvinho\Virus\Virus\Virus;
use mysqli;
use function Kelvinho\Virus\filter;

/**
 * Class User
 *
 * Represents a user. The representation of this will be stored in table users only. No data is stored on disk.
 * But if needed in the future, it should be placed at DATA_FILE/users/{user_id}/
 *
 * @package Kelvinho\Virus\User
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class User {
    private string $user_handle;
    private string $name;
    private int $timezone = 0;
    private bool $hold;
    private mysqli $mysqli;

    /**
     * User constructor.
     * @param string $user_handle
     * @param mysqli $mysqli
     * @internal
     */
    public function __construct(string $user_handle, mysqli $mysqli) {
        $this->user_handle = $user_handle;
        $this->mysqli = $mysqli;
        $this->fetchData();
    }

    private function fetchData(): void {
        $answer = $this->mysqli->query("select name, timezone, hold from users where user_handle = \"$this->user_handle\"");
        if ($row = $answer->fetch_assoc()) {
            $this->name = $row["name"];
            $this->timezone = $row["timezone"];
            $this->hold = $row["hold"];
        }
    }

    public function getTimezone(): int {
        return $this->timezone;
    }

    public function removeHold() {
        $this->hold = false;
    }

    public function applyHold() {
        $this->hold = true;
    }

    public function isHold(): bool {
        return $this->hold;
    }

    /**
     * Saves state of user.
     */
    public function saveState(): void {
        $this->mysqli->query("update users set timezone = $this->timezone, hold = b'" . ($this->hold ? "1" : "0") . "' where user_handle = \"$this->user_handle\"");
    }

    /**
     * Get array of index -> {"virus_id" -> "{virus_id}", "last_ping" -> "{last_ping}"}
     *
     * @param int $virusStatus
     * @return array
     */
    public function getViruses(int $virusStatus): array {
        switch ($virusStatus) {
            case Virus::VIRUS_ALL:
                $viruses = [];
                $answer = $this->mysqli->query("select virus_id, last_ping from viruses where user_handle = \"$this->user_handle\"");
                if ($answer) {
                    while ($row = $answer->fetch_assoc()) {
                        array_push($viruses, array("virus_id" => $row["virus_id"], "last_ping" => $row["last_ping"]));
                    }
                }
                return $viruses;
            default:
                return filter($this->getViruses(Virus::VIRUS_ALL), function ($data, /** @noinspection PhpUnusedParameterInspection */ $key, int $virusStatus) {
                    return Virus::getState($data["last_ping"]) == $virusStatus;
                }, $virusStatus);
        }
    }
}
