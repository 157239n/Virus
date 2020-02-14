<?php

namespace Kelvinho\Virus\Id;

use mysqli;

/**
 * Class IdGeneratorImp
 * Responsible for generating new virus_id and attack_id, making sure a value has not been used before.
 *
 * @package Kelvinho\Virus\Id
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class IdGeneratorImp implements IdGenerator {
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Generates a new virus id.
     *
     * @return string
     */
    public function newVirusId(): string {
        return IdGeneratorImp::newId("virus_id", "viruses");
    }

    /**
     * Creates a new id based on SHA256 hash. This will check against the existing database to make sure there are no collisions.
     * If there are a collision then it will generate a new one automatically.
     *
     * @param string $field The field to check against, like attack_id, or virus_id
     * @param string $table The table to check against, like attacks, viruses
     * @return string The new id
     */
    private function newId(string $field, string $table) {
        $id = hash("sha256", rand() + time());
        $answer = $this->mysqli->query("select $field from $table where $field = $id");
        $hasId = false;
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                $hasId = true;
            }
        }
        if ($hasId) {
            return IdGeneratorImp::newId($field, $table);
        } else {
            return $id;
        }
    }

    /**
     * Generates a new attack id.
     *
     * @return string
     */
    public function newAttackId(): string {
        return IdGeneratorImp::newId("attack_id", "attacks");
    }
}