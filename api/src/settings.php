<?php

use Respect\Validation\Validator as v;

//TODO: add .env support
return [
    'settings' => [
        // Database settings
        'database' => [ //TODO: .env
            'engine' => 'mysql',
            'host' => 'db', // name of the docker container running the database
            'database' => 'jmp',
            'username' => 'jmp_user',
            'password' => 'pass4dev',
            'port' => '3306',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,
            ]
        ],
        'validation' => [
            'login' => [
                'username' => v::notEmpty()->noWhitespace()->contains('.')->length(1, 101),
                'password' => v::notEmpty()->length(6, 255),
            ]
        ],
        'jwt' => [
            //TODO: save as cookie?
            'secret' => 'secret',//TODO: .env
            'secure' => false, //TODO: secure
//            "header" => "Authorization",
//            "regexp" => "/Token\s+(.*)$/i", //TODO: required?
//            'passthrough' => ['OPTIONS'] //TODO: required?
        ],
        // Monolog settings
        'logger' => [
            'name' => 'JMP', //TODO: .env
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log', //TODO: docker logging?
            'level' => \Monolog\Logger::DEBUG, //TODO: .env
        ],
    ]
];
