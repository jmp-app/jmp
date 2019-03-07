<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Middleware\JwtAuthentication;

/**
 * @var $container \Psr\Container\ContainerInterface
 */
$container = $app->getContainer();

// Database

$container['database'] = function (\Psr\Container\ContainerInterface $container) {
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

$container['membershipService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\MembershipService($container);
};

$container['registrationStateService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\RegistrationStateService($container);
};

$container['registrationService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\RegistrationService($container);
};

$container['eventTypeService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\EventTypeService($container);
};

$container['userService'] = function (\Psr\Container\ContainerInterface $container) {
    return new \JMP\Services\UserService($container);
};

// Logger
$container['logger'] = function (\Psr\Container\ContainerInterface $container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Logger($settings['name']);

    $logger->pushProcessor(new WebProcessor());
    $logger->pushProcessor(new IntrospectionProcessor());

    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));

    if ($settings['stdout']) {
        $logger->pushHandler(new StreamHandler('php://stdout', $settings['level']));
    }

    return $logger;
};

// Custom Error Handler
$container['phpErrorHandler'] = $container['errorHandler'] = function (\Psr\Container\ContainerInterface $container) {
    return function (RequestInterface $request, ResponseInterface $response, Exception $exception) use ($container): ResponseInterface {

        $logger = $container->get('logger');
        $debug = $container->get('settings')['displayErrorDetails'];

        $logger->addError($exception->getMessage());

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->write(json_encode([
                'errors' => [
                    'internalServerError' => $debug ? $exception->getMessage() : 'Internal Server Error'
                ]
            ]));
    };
};
