<?php

namespace Kelvinho\Virus {
    /**
     * Creates a new id based on SHA256 hash. This will check against the existing database to make sure there are no collisions.
     * If there are a collision then it will generate a new one automatically.
     *
     * @param string $field The field to check against, like attack_id, or virus_id
     * @param string $table The table to check against, like attacks, viruses
     * @return string The new id
     */
    function newId(string $field, string $table) {
        $id = hash("sha256", rand());
        $mysqli = db();
        if ($mysqli->connect_errno) {
            logMysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select $field from $table where $field = $id");
        $mysqli->close();
        $hasId = false;
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                $hasId = true;
            }
        }
        if ($hasId) {
            return newId($field, $table);
        } else {
            return $id;
        }
    }

    /**
     * Creates a new virus id. Guarantees to be unique.
     *
     * @return string The 64-character-long virus id
     */
    function newVirusId(): string {
        return newId("virus_id", "viruses");
    }

    /**
     * Creates a new attack id. Guarantees to be unique.
     *
     * @return string The 64-character-long attack id
     */
    function newAttackId(): string {
        return newId("attack_id", "attacks");
    }
}