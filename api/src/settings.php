<?php

return [
    'settings' => [
        // Database settings
        'database' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'jmp',
            'username' => 'jmp_user',
            'password' => 'pass4dev',
            'port' => '3306',
            'charset' => 'utf8',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ]
    ]
];
