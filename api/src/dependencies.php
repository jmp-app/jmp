<?php


use Tuupola\Middleware\JwtAuthentication;

$container = $app->getContainer();

// Database

$container['database'] = function ($container) {
    $config = $container->get('settings')['database'];

    $dsn = "{$config['engine']}:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

    return new PDO($dsn, $config['username'], $config['password'], $config['options']);
};

// Middlewares

$container['jwt'] = function (\Psr\Container\ContainerInterface $container) {

    $jwt_settings = $container->get('settings')['jwt'];

    return new JwtAuthentication($jwt_settings);
};

// Services

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

// Logger
// TODO (dominik): Make logger working with docker / logger required?
$container['logger'] = function (\Psr\Container\ContainerInterface $container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};
