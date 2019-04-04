<?php

use jmp\Services\Auth;
use jmp\Services\EventService;
use jmp\Services\EventTypeService;
use jmp\Services\GroupService;
use jmp\Services\MembershipService;
use jmp\Services\RegistrationService;
use jmp\Services\RegistrationStateService;
use jmp\Services\UserService;
use jmp\Services\ValidationService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Middleware\JwtAuthentication;

/**
 * @var $container ContainerInterface
 */
$container = $app->getContainer();

// Database

$container['database'] = function (ContainerInterface $container) {
    $config = $container->get('settings')['database'];

    $dsn = "{$config['engine']}:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

    return new PDO($dsn, $config['username'], $config['password'], $config['options']);
};

// Middlewares

$container['jwt'] = function (ContainerInterface $container) {

    $jwt_settings = $container->get('settings')['jwt'];

    return new JwtAuthentication($jwt_settings);
};

// Services

$container['auth'] = function (ContainerInterface $container) {
    return new Auth($container);
};

$container['eventService'] = function (ContainerInterface $container) {
    return new EventService($container);
};

$container['groupService'] = function (ContainerInterface $container) {
    return new GroupService($container);
};

$container['membershipService'] = function (ContainerInterface $container) {
    return new MembershipService($container);
};

$container['registrationStateService'] = function (ContainerInterface $container) {
    return new RegistrationStateService($container);
};

$container['registrationService'] = function (ContainerInterface $container) {
    return new RegistrationService($container);
};

$container['eventTypeService'] = function (ContainerInterface $container) {
    return new EventTypeService($container);
};

$container['userService'] = function (ContainerInterface $container) {
    return new UserService($container);
};

$container['validationService'] = function (ContainerInterface $container) {
    return new ValidationService($container);
};

// Logger
$container['logger'] = function (ContainerInterface $container) {
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
$container['phpErrorHandler'] = $container['errorHandler'] = function (ContainerInterface $container) {
    return function (RequestInterface $request, ResponseInterface $response, $exception) use ($container): ResponseInterface {

        /** @var Logger $logger */
        $logger = $container->get('logger');
        $debug = $container->get('settings')['displayErrorDetails'];

        $logger->error('Message: {' . $exception->getMessage() . '} Trace: {' . $exception->getTrace() . '}');

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->write(json_encode([
                'errors' => [
                    'internalServerError' => $debug ? $exception->getMessage() : 'Internal Server Error',
                    'trace' => $debug ? $exception->getTrace() : null
                ]
            ]));
    };
};
