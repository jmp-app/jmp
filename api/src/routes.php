<?php

use DavidePastore\Slim\Validation\Validation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;


// API Routes - version 1
$app->group('/v1', function () {

    $this->get('/hello', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Hello World");
        return $response;
    });


    $this->get('/test', function (Request $request, Response $response, array $args) {
        $config = $this->get('settings')['database'];

        $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";;

        $response->getBody()->write($dsn . phpinfo());
        return $response;
    });

    $validators = [
        'username' => v::alnum()->noWhitespace()->length(1, 101),
        'password' => v::length(6, 255)
    ];
    $this->post('/login', \JMP\Controllers\LoginController::class . ':login')->add(
        new Validation($this->getContainer()['settings']['validation']['user'])
    );

});