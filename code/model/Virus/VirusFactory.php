<?php

namespace Kelvinho\Virus\Virus;

use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Ids;
use Kelvinho\Virus\Logs;
use Kelvinho\Virus\Session;
use function Kelvinho\Virus\db;

class VirusFactory {
    private Session $session;
    private AttackFactory $attackFactory;

    public function __construct(Session $session, AttackFactory $attackFactory) {
        $this->session = $session;
        $this->attackFactory = $attackFactory;
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
        $virus_id = Ids::newVirusId();
        if ($user_handle == null) {
            $user_handle = $this->session->get("user_handle");
        }
        $mysqli = db();
        if ($mysqli->connect_errno) Logs::mysql($mysqli->connect_error);
        $mysqli->query("insert into viruses (virus_id, user_handle, last_ping, name, active, type) values (\"$virus_id\", \"" . $user_handle . "\", 0, \"(not set)\", b'0', b'" . ($standalone ? "0" : "1") . "')");
        $mysqli->close();
        mkdir(DATA_FILE . "/viruses/$virus_id");
        touch(DATA_FILE . "/viruses/$virus_id/profile.txt");

        return $this->get($virus_id);
    }
}