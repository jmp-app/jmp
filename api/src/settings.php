<?php

use Respect\Validation\Validator as v;

// Define root path
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DS);

// Load .env file
if (file_exists(ROOT . '.env')) {
    $dotenv = new Dotenv\Dotenv(ROOT);
    $dotenv->load();
}

return [
    'settings' => [

        // App settings
        'app' => [
            'name' => getenv('APP_NAME'),
            'url' => getenv('APP_URL'),
            'env' => getenv('APP_ENV'),
        ],

        // Database settings
        'database' => [
            'engine' => getenv('DB_ENGINE'),
            'host' => getenv('DB_HOST'), // name of the docker container running the database
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'port' => getenv('DB_PORT'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,
            ]
        ],

        // Input validations
        'validation' => [
            'login' => [
                'username' => v::notEmpty()->noWhitespace()->contains('.')->length(1, 101),
                'password' => v::notEmpty()->length(6, 255),
            ]
        ],

        // JWT settings
        'jwt' => [
            'secret' => getenv('JWT_SECRET'),
            'secure' => getenv('JWT_SECURE') === 'true' ? true : false,
        ],

        // Monolog settings
        'logger' => [
            'name' => getenv('APP_NAME'),
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ]
];
