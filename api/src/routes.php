<?php

use DavidePastore\Slim\Validation\Validation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


// API Routes - version 1
$app->group('/v1', function () {

    $jwtMiddleware = $this->getContainer()->get('jwt');

    $this->get('/hello', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Hello World");
        return $response;
    });


    $this->get('/test', function (Request $request, Response $response, array $args) {
        $config = $this->get('settings')['database'];

        $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";;

        $response->getBody()->write($dsn . phpinfo());
        return $response;
    })->add($jwtMiddleware);


    $this->post('/login', \JMP\Controllers\LoginController::class . ':login')->add(
        new Validation($this->getContainer()['settings']['validation']['login'])
    );

});
