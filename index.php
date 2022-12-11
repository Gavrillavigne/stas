<?php

ini_set('display_errors',1);
error_reporting(E_ERROR);

use common\components\Db;
use Dotenv\Dotenv;

define('ROOT', dirname(__FILE__));

// autoload
require_once __DIR__ . '/components/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$db = Db::getConnection();