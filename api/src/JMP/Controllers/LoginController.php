<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

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
    public function login(Request $request, Response $response, array $args): Response
    {

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors]);
        }


        $body = $request->getParsedBody();

        if ($user = $this->auth->attempt($body['username'], $body['password'])) {
            $data = [
                'token' => $this->auth->generateToken($body),
                'user' => $user
            ];
            return $response->withJson($data);
        } else {
            return $response->withJson(['errors' => ['username or password' => ['is invalid']]], 400);
        }
    }

}