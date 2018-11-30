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
    protected $auth;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        //$this->db = $container->get('database');
        $this->auth = $container->get('auth');
    }

    public function login($request, $response, $args)
    {

        //$db = $this->container->get('database');

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson($errors);
        }


        $body = $request->getParsedBody();

        if ($this->auth->attempt($body['username'], $body['password'])) {
            $body['token'] = $this->auth->generateToken($body);
            return $response
                ->withJson($body);
        } else {
            return $response->withJson(['errors' => ['email or password' => ['is invalid']]], 422);
        }
        // select user by username

        // hash password from request

        // verify passwords

        // return unauthorized or user

    }

}