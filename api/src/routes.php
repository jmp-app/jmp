<?php

use DavidePastore\Slim\Validation\Validation;
use JMP\Controllers\EventsController;
use JMP\Controllers\LoginController;
use JMP\Controllers\UsersController;

// API Routes - version 1
$app->group('/v1', function () {

    $jwtMiddleware = $this->getContainer()->get('jwt');

    $this->post('/login', LoginController::class . ':login')
        ->add(
            new \JMP\Middleware\ValidationErrorResponseBuilder()
        )->add(
            new Validation(
                $this->getContainer()['validation']['login'],
                $this->getContainer()['validation']['loginTranslation']
            )
        );

    $this->get('/events', EventsController::class . ':listEvents')
        ->add(new \JMP\Middleware\ValidationErrorResponseBuilder())
        ->add(
            new Validation($this->getContainer()['validation']['listEvents'])
        )->add($jwtMiddleware);

    $this->post('/users', UsersController::class . ':createUser')
        ->add(new \JMP\Middleware\ValidationErrorResponseBuilder())
        ->add(
            new Validation($this->getContainer()['validation']['createUser'])
        )->add($jwtMiddleware);

});