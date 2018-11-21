<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// API Routes - version 1
$app->group('/v1', function() {

    $this->get('/hello', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Hello World");
        return $response;
    });

});