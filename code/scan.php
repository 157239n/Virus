<?php
/*
This is to scan for all viruses to see what is their last ping time. If it's too far behind then change it to being not
active, and logs the transition in the database. This is not supposed to be called from any of the running script.
Rather, it's supposed to be triggered by a shell script in the entry point every span of time, like 3 minutes or so. I
don't really mind if the user 'accidentally' invoke this, because that will just get refreshed anyway and presents
virtually no harm.
*/

use Kelvinho\Virus\Virus;
use function Kelvinho\Virus\db;
use function Kelvinho\Virus\logMysql;

require_once(__DIR__ . "/autoload.php");

// loops through all viruses data, get active field and last_ping. Do math, then figure whether to write to the database

/**
 * Limits each virus to only have 1000 entries in the uptimes table
 *
 * @param mysqli $mysqli
 * @param string $virus_id
 */
function trimEntry(mysqli $mysqli, string $virus_id) {
    $answer = $mysqli->query("select count(*) as count, virus_id from uptimes where virus_id = \"$virus_id\" group by virus_id");
    if ($answer) {
        $row = $answer->fetch_assoc();
        \Kelvinho\Virus\log("#" . $row["virus_id"]);
        if ($row["count"] > 1000) {
            $mysqli->query("delete from uptimes where virus_id = \"$virus_id\" order by unix_time limit " . ($row["count"] - 1000));
        }
    }
}

//\Kelvinho\Virus\log("@" . getenv("MYSQL_HOST") . " " . getenv("MYSQL_USER") . " " . getenv("MYSQL_PASSWORD") . " " . getenv("MYSQL_DATABASE"));

$mysqli = db();
if ($mysqli->connect_errno) {
    logMysql($mysqli->connect_error);
}
$answer = $mysqli->query("select virus_id, last_ping, cast(active as unsigned integer) as activeI from viruses");
if ($answer) {
    while ($row = $answer->fetch_assoc()) {
        $virus_id = $row["virus_id"];
        $last_ping = (int)$row["last_ping"];
        $active = (int)$row["activeI"];
        //echo "virus_id: $virus_id, active: $active, state: " . Virus::getState($last_ping) . "\n";
        if ($active == 0) { // previously deemed not active, so check if last ping is considered active
            if (Virus::getState($last_ping) == Virus::VIRUS_ACTIVE) {
                trimEntry($mysqli, $virus_id);
                $mysqli->query("update viruses set active = b'1' where virus_id = \"$virus_id\"");
                $mysqli->query("insert into uptimes (virus_id, unix_time, active) values (\"$virus_id\", " . time() . ", b'1')");
            }
        } else { // previously deemed active, so check if last ping is considered inactive
            if (Virus::getState($last_ping) != Virus::VIRUS_ACTIVE) {
                trimEntry($mysqli, $virus_id);
                $mysqli->query("update viruses set active = b'0' where virus_id = \"$virus_id\"");
                $mysqli->query("insert into uptimes (virus_id, unix_time, active) values (\"$virus_id\", " . time() . ", b'0')");
            }
        }
    }
}
$mysqli->close();