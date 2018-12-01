<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 29.11.18
 * Time: 19:17
 */
class LoginController
{
    /**
     * @var \JMP\Services\Auth
     */
    protected $auth;


    /**
     * LoginController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
    }

    /**
     * Response contains an user with a token if the login was successful
     * @param $request Request
     * @param $response Response
     * @param $args array
     * @return Response
     * @throws \Exception
     */
    public function login($request, $response, $args)
    {

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors]);
        }


        $body = $request->getParsedBody();

        if ($user = $this->auth->attempt($body['username'], $body['password'])) {
            $user['token'] = $this->auth->generateToken($body);
            return $response->withJson($user);
        } else {
            return $response->withJson(['errors' => ['email or password' => ['is invalid']]], 400);
        }
    }

}