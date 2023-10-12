<?php

ini_set('display_errors',1);
error_reporting(E_ERROR);

use infrastructure\components\Db;
use Dotenv\Dotenv;

define('ROOT', dirname(__DIR__));

require_once __DIR__ . '/../infrastructure/config/autoload.php';
require_once __DIR__ . '/../infrastructure/config/Router.php';
require_once __DIR__ . '/../infrastructure/components/Db.php';

$dotenv = new Dotenv(__DIR__ . '/..');
$dotenv->load();

$router = new Router();
$router->run();
$db = Db::getConnection();