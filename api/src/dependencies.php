<?php


use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Odan\Slim\Session\Session;
use Tuupola\Middleware\JwtAuthentication;

$container = $app->getContainer();

$container['database'] = function ($container) {
    $config = $container->get('settings')['database'];

    $dsn = "{$config['engine']}:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

    return new PDO($dsn, $config['username'], $config['password'], $config['options']);
};

$container['session'] = function (\Psr\Container\ContainerInterface $container) {
    $settings = $container->get('settings')['session'];
    $adapter = new PhpSessionAdapter();
    $session = new Session($adapter);
    $session->setOptions($settings);

    return $session;
};

$container['jwt'] = function (\Psr\Container\ContainerInterface $container) {

    $jwt_settings = $container->get('settings')['jwt'];

    return new JwtAuthentication($jwt_settings);
};

$container['auth'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\Auth($container);
};

$container['eventService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\EventService($container);
};

$container['groupService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\GroupService($container);
};

$container['registrationStateService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\RegistrationStateService($container);
};

$container['eventTypeService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\EventTypeService($container);
};

$container['userService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\UserService($container);
};

$container['nullFilter'] = function () {
    return function ($value) {
        return $value !== null;
    };
};

// monolog
$container['logger'] = function (\Psr\Container\ContainerInterface $container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};
