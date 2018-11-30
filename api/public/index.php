<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = require __DIR__ . '/../src/settings.php';

$app = new \Slim\App($settings);


// Set up dependencies
require __DIR__ . '/../src/dependencies.php';


require __DIR__ . '/../src/routes.php';

$app->run();
