<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/Project');

require __DIR__ . '/Connect.php';
require __DIR__ . '/api/General.php';
require __DIR__ . '/api/member.php';
require __DIR__ . '/api/admin.php';

$app->run();