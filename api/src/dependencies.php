<?php

$container = $app->getContainer();

$container['database'] = function ($c) {
    $config = $c->get('settings')['database'];

    $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";;

    return new PDO($dsn, $config['username'], $config['password'], $config['options']);
};