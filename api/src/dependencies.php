<?php


use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Odan\Slim\Session\Session;
use Tuupola\Middleware\JwtAuthentication;

$container = $app->getContainer();

$container['database'] = function ($container) {
    $config = $container->get('settings')['database'];

    $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";;

    return new PDO($dsn, $config['username'], $config['password'], $config['options']);
};

$container['session'] = function ($container) {
    $settings = $container->get('settings')['session'];
    $adapter = new PhpSessionAdapter();
    $session = new Session($adapter);
    $session->setOptions($settings);

    return $session;
};

$container['jwt'] = function ($c) {

    $jws_settings = $c->get('settings')['jwt'];

    return new JwtAuthentication($jws_settings);
};

$container['auth'] = function ($container) {
    return new \JMP\Services\Auth($container);
};
