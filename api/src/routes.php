<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// API Routes - version 1
$app->group('/v1', function () {

    $this->get('/hello', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Hello World");
        return $response;
    });


    $this->get('/login', function (Request $request, Response $response, array $args) {
        return $response->withJson(['username' => 'bla']);
    });

    $this->get('/test', \JMP\Controllers\LoginController::class . ':login');

});