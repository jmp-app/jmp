<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;


/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 29.11.18
 * Time: 19:17
 */
class LoginController
{
    protected $db;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        //$this->db = $container->get('database');
    }

    public function login($request, $response, $args)
    {
        //$db = $this->container->get('database');

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson($errors);
        }

        $body = $request->getParsedBody();


        return $response->withJson($body);
    }

}