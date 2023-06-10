<?php

ini_set('display_errors',1);
error_reporting(E_ERROR);

use common\components\Db;
use Dotenv\Dotenv;
use components\Router;

define('ROOT', dirname(__FILE__));

require_once __DIR__ . '/components/autoload.php';
require_once __DIR__ . '/components/Router.php';
require_once __DIR__ . '/common/components/Db.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();


$router = new Router();
$router->run();
$db = Db::getConnection();