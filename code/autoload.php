<?php /** @noinspection PhpIncludeInspection */

// NOTICE: remember to close the mysql connection after including this file

use Kelvinho\Virus\Attack\AttackFactoryImp;
use Kelvinho\Virus\Auth\AuthenticatorImp;
use Kelvinho\Virus\Id\IdGeneratorImp;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Network\Router;
use Kelvinho\Virus\Network\WhitelistFactory;
use Kelvinho\Virus\Session\Session;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\User\UserFactoryImp;
use Kelvinho\Virus\Virus\VirusFactoryImp;

require_once(__DIR__ . "/consts.php");
require_once(__DIR__ . "/basics.php");
foreach (glob(__DIR__ . "/model/*/*.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/model/*/*/*.php") as $file) require_once($file);
require_once(__DIR__ . "/model/Attack/autoload.php");

session_start();
$session = new Session();

$mysqli = new mysqli(getenv("MYSQL_HOST"), getenv("MYSQL_USER"), getenv("MYSQL_PASSWORD"), getenv("MYSQL_DATABASE"));
if ($mysqli->connect_errno) Logs::error("Mysql failed. Info: $mysqli->connect_error");

$requestData = new RequestData();
$whitelistFactory = new WhitelistFactory();
$idGenerator = new IdGeneratorImp($mysqli);
$userFactory = new UserFactoryImp($mysqli);
$attackFactory = new AttackFactoryImp();
$virusFactory = new VirusFactoryImp($session, $attackFactory, $idGenerator, $mysqli);
$attackFactory->addContext($requestData, $session, $userFactory, $virusFactory, $idGenerator, $mysqli);
$router = new Router($requestData);
$authenticator = new AuthenticatorImp($session, $mysqli);

foreach (glob(__DIR__ . "/routes/*.php") as $file) require_once($file);
