<?php

namespace Kelvinho\Virus\User;

use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Usage\Usage;
use Kelvinho\Virus\Usage\UsageFactory;
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
    private int $timezone;
    private bool $hold;
    private mysqli $mysqli;
    private Usage $usage;
    private UsageFactory $usageFactory;
    private int $unpaidAmount;

    /**
     * User constructor.
     * @param string $user_handle
     * @param mysqli $mysqli
     * @param UsageFactory $usageFactory
     * @internal
     */
    public function __construct(string $user_handle, mysqli $mysqli, UsageFactory $usageFactory) {
        $this->user_handle = $user_handle;
        $this->mysqli = $mysqli;
        $this->usageFactory = $usageFactory;
        $this->loadState();
    }

    private function loadState(): void {
        if (!$answer = $this->mysqli->query("select name, timezone, hold, resource_usage_id, unpaid_amount from users where user_handle = \"$this->user_handle\"")) throw new UserNotFound();
        if (!$row = $answer->fetch_assoc()) throw new UserNotFound();
        $this->name = $row["name"];
        $this->timezone = $row["timezone"];
        $this->hold = $row["hold"];
        $this->usage = $this->usageFactory->get($row["resource_usage_id"]);
        $this->unpaidAmount = $row["unpaid_amount"];
    }

    public function getTimezone(): int {
        return $this->timezone;
    }

    public function setTimezone(int $timezone): void {
        $this->timezone = $timezone;
    }

    public function removeHold(): void {
        $this->hold = false;
    }

    public function applyHold(): void {
        $this->hold = true;
    }

    public function isHold(): bool {
        return $this->hold;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getHandle(): string {
        return $this->user_handle;
    }

    public function getUnpaidAmount(): int {
        return $this->unpaidAmount;
    }

    public function setUnpaidAmount(int $cents): void {
        $this->unpaidAmount = $cents;
    }

    /**
     * Whether this user is allowed to launch new attacks. If not then the user must pay before he/she can continue.
     *
     * @return bool
     */
    public function allowed(): bool {
        return $this->usage->allowed($this->unpaidAmount);
    }

    /**
     * Saves state of user.
     */
    public function saveState(): void {
        if (!$this->mysqli->query("update users set name = \"" . $this->mysqli->escape_string($this->name) . "\", timezone = $this->timezone, hold = b'" . ($this->hold ? "1" : "0") . "' where user_handle = \"$this->user_handle\"")) Logs::mysql($this->mysqli);
    }

    /**
     * Get array of index -> {"virus_id" -> "{virus_id}", "last_ping" -> "{last_ping}"}
     *
     * @param int $virusStatus
     * @return array
     */
    public function getViruses(int $virusStatus = Virus::VIRUS_ALL): array {
        switch ($virusStatus) {
            case Virus::VIRUS_ALL:
                $viruses = [];
                if (!$answer = $this->mysqli->query("select virus_id, last_ping from viruses where user_handle = \"$this->user_handle\"")) return [];
                while ($row = $answer->fetch_assoc())
                    array_push($viruses, array("virus_id" => $row["virus_id"], "last_ping" => $row["last_ping"]));
                return $viruses;
            default:
                return filter($this->getViruses(Virus::VIRUS_ALL), function ($data, /** @noinspection PhpUnusedParameterInspection */ $key, int $virusStatus) {
                    return Virus::getState($data["last_ping"]) == $virusStatus;
                }, $virusStatus);
        }
    }

    public function usage(): Usage {
        return $this->usage;
    }
}
