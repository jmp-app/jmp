<?php

use DavidePastore\Slim\Validation\Validation;

// API Routes - version 1
$app->group('/v1', function () {

    $jwtMiddleware = $this->getContainer()->get('jwt');

    $this->post('/login', \JMP\Controllers\LoginController::class . ':login')->add(
        new Validation($this->getContainer()['settings']['validation']['login'])//TODO: translation for password validation
    );

    $this->get('/events', \JMP\Controllers\EventController::class . ':listEvents')->add(
        new Validation($this->getContainer()['settings']['validation']['listEvents'])
    )->add(
        $jwtMiddleware
    );

});
