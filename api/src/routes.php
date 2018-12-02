<?php

use DavidePastore\Slim\Validation\Validation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// API Routes - version 1
$app->group('/v1', function () {

    $jwtMiddleware = $this->getContainer()->get('jwt');

    $this->post('/login', \JMP\Controllers\LoginController::class . ':login')->add(
        new Validation($this->getContainer()['settings']['validation']['login'])
    );

});
