<?php

namespace Kelvinho\Virus\Controller;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\Virus;
use function Kelvinho\Virus\checkVariable;

class Helper {
    public static function verifyIds($virus_id, $attack_id) {
        $virus_id = checkVariable($virus_id);
        $attack_id = checkVariable($attack_id);

        if (!Virus::exists($virus_id)) {
            Header::forbidden();
        }

        if (!AttackInterface::exists($attack_id)) {
            Header::forbidden();
        }
        return [$virus_id, $attack_id];
    }
}