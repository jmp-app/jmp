<?php

use DavidePastore\Slim\Validation\Validation;
use JMP\Controllers\EventsController;
use JMP\Controllers\LoginController;
use JMP\Controllers\UsersController;

// API Routes - version 1
$app->group('/v1', function () {

    $jwtMiddleware = $this->getContainer()->get('jwt');

    $this->post('/login', LoginController::class . ':login')->add(
        new Validation($this->getContainer()['settings']['validation']['login'])// TODO (dominik): translation for password validation
    );

    $this->get('/events', EventsController::class . ':listEvents')->add(
        new Validation($this->getContainer()['settings']['validation']['listEvents'])
    )->add($jwtMiddleware);

    $this->post('/users', UsersController::class . ':createUser')->add(
        new Validation($this->getContainer()['settings']['validation']['createUser'])
    )->add($jwtMiddleware);

});
