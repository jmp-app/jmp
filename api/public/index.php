<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = new \Slim\App;

require __DIR__ . '/../src/routes.php';

$app->run();
