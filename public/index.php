<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\Cors;


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../db/db.php';
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
require __DIR__ .'/../routes/initdb.php';
require __DIR__ .'/../routes/aircraft.php';
require __DIR__ .'/../routes/queue.php';

$app->run();