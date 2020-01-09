<?php

namespace Kelvinho\Virus\Virus;

use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Id\IdGenerator;
use Kelvinho\Virus\Id\IdGeneratorImp;
use Kelvinho\Virus\Session\Session;
use function Kelvinho\Virus\db;

/**
 * Class VirusFactory. Responsible for instantiating viruses and creating new ones.
 *
 * @package Kelvinho\Virus\Virus
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class VirusFactory {
    private Session $session;
    private AttackFactory $attackFactory;
    private IdGenerator $idGenerator;

    public function __construct(Session $session, AttackFactory $attackFactory, IdGenerator $idGenerator) {
        $this->session = $session;
        $this->attackFactory = $attackFactory;
        $this->idGenerator = $idGenerator;
    }

    /**
     * Gets a virus from a virus id. If there are no viruses with that id, it returns null
     *
     * @param string $virus_id The virus id
     * @return Virus|null THe virus
     */
    public function get($virus_id): Virus {
        if (!Virus::exists($virus_id)) throw new VirusNotFound();
        return new Virus($virus_id, $this->attackFactory);
    }

    /**
     * Creates a new virus under the current user
     *
     * @param string $user_handle
     * @param bool $standalone
     * @return Virus The virus
     */
    public function new(string $user_handle = null, bool $standalone = true): Virus {
        $virus_id = $this->idGenerator->newVirusId();
        if ($user_handle == null) {
            $user_handle = $this->session->get("user_handle");
        }
        $mysqli = db();
        $mysqli->query("insert into viruses (virus_id, user_handle, last_ping, name, active, type) values (\"$virus_id\", \"" . $user_handle . "\", 0, \"(not set)\", b'0', b'" . ($standalone ? "0" : "1") . "')");
        $mysqli->close();
        mkdir(DATA_FILE . "/viruses/$virus_id");
        touch(DATA_FILE . "/viruses/$virus_id/profile.txt");

        return $this->get($virus_id);
    }
}