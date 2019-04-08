<?php

// Define root path
use Dotenv\Dotenv;
use Monolog\Logger;

defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DS);

// Load .env file
if (file_exists(ROOT . '.env')) {
    $dotenv = Dotenv::create(ROOT);
    $dotenv->load();
}

// Load db.env file
$db_dotenv_file = 'db.env';
if (file_exists(ROOT . $db_dotenv_file)) {
    $db_dotenv = Dotenv::create(ROOT, $db_dotenv_file);
    $db_dotenv->load();
}

/**Returns the log level int depending on the log level name
 * Default is @param string $logLevel
 * @return int
 * @see \Monolog\Logger::ERROR
 */
function getLogLevel(string $logLevel): int
{
    foreach (Logger::getLevels() as $key => $level) {
        if (strcasecmp($logLevel, $level) === 0) {
            return $key;
        }
    }
    return Logger::ERROR;
}

return [
    'settings' => [

        'displayErrorDetails' => getenv('APP_DEBUG') === 'true' ? true : false, // set to false in production

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

        // JWT settings
        'jwt' => [
            'secret' => getenv('JWT_SECRET'),
            'secure' => getenv('JWT_SECURE') === 'true' ? true : false,
            'expiration' => getenv('JWT_EXPIRATION'),
        ],

        // Auth settings
        'auth' => [
            'subjectIdentifier' => 'id'
        ],

        // Monolog settings
        'logger' => [
            'name' => getenv('APP_NAME'),
            'path' => __DIR__ . '/../' . getenv('APP_LOG_FILE'),
            'stdout' => getenv('APP_LOG_STDOUT') === "true",
            'level' => getLogLevel(getenv('APP_LOG_LEVEL')),
        ],
        'cors' => [
            'origins' => getenv('CORS_ALLOWED_ORIGINS'),
            'headers' => getenv('CORS_ALLOWED_HEADERS'),
            'methods' => getenv('CORS_ALLOWED_METHODS')
        ]
    ]
];
