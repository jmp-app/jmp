<?php

$container = $app->getContainer();

$container['database'] = function ($c) {
    $settings = $c->get('database');

    $dsn = "${settings['driver']}:host=${settings['host']};dbname=${settings['database']};charset=${settings['charset']}";

    return new PDO($dsn, $settings['username'], $settings['password'], $settings['options']);
};