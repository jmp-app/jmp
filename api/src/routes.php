<?php

use DavidePastore\Slim\Validation\Validation;
use JMP\Controllers\EventsController;
use JMP\Controllers\LoginController;
use JMP\Controllers\UsersController;
use JMP\Middleware\AuthenticationMiddleware;
use JMP\Middleware\ValidationErrorResponseBuilder;

// API Routes - version 1
$app->group('/v1', function () {

    $container = $this->getContainer();
    $jwtMiddleware = $container->get('jwt');

    $this->post('/login', LoginController::class . ':login')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation(
            $this->getContainer()['validation']['login'],
            $this->getContainer()['validation']['loginTranslation']
        ))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::OPEN));

    $this->get('/events', EventsController::class . ':listEvents')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($this->getContainer()['validation']['listEvents']))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->post('/users', UsersController::class . ':createUser')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($this->getContainer()['validation']['createUser']))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

});