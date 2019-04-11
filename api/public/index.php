<?php

// Required for jwt with apache web servers
use Slim\App;

$_SERVER['HTTP_AUTHORIZATION'] = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : $_GET['Authorization'];

require __DIR__ . '/../vendor/autoload.php';

session_start();
session_regenerate_id();

// Set settings and instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Add validation
require __DIR__ . '/../src/validation.php';


// Register routes
require __DIR__ . '/../src/routes.php';

$app->run();
