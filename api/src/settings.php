<?php

use Respect\Validation\Validator as v;

return [
    'settings' => [
        // Database settings
        'database' => [
            'engine' => 'mysql',
            'host' => 'localhost',
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
            'secret' => 'secret',
            'secure' => false,
            "header" => "Authorization",
            "regexp" => "/Token\s+(.*)$/i",
            'passthrough' => ['OPTIONS']
        ],
    ]
];
