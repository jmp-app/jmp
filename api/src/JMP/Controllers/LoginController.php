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
     * @var callable
     */
    private $nullFilter;


    /**
     * LoginController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->nullFilter = $container->get('nullFilter');
    }

    /**
     * Response contains an user with a token if the login was successful
     * @param $request Request
     * @param $response Response
     * @return Response
     * @throws \Exception
     */
    public function login(Request $request, Response $response): Response
    {

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors], 400);
        }


        $body = $request->getParsedBody();

        $optional = $this->auth->attempt($body['username'], $body['password']);

        if ($optional->isSuccess()) {
            $data = [
                'token' => $this->auth->generateToken($body),
                'user' => array_filter($optional->getData(), $this->nullFilter)
            ];
            return $response->withJson($data);
        } else {
            return $response->withJson(['errors' => ['Username or password is incorrect' => ['is invalid']]], 403);
        }
    }

}