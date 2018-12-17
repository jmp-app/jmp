<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;
use JMP\Utils\Converter;
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
     * Response contains an user with a token if the login was successful.
     * Else the response contains an error with an appropriate error code.
     * @param $request Request
     * @param $response Response
     * @return Response
     * @throws \Exception
     */
    public function login(Request $request, Response $response): Response
    {

        // check for validation errors
        if ($request->getAttribute('has_errors')) {
            return $response;
        }

        $body = $request->getParsedBody();

        // authenticate the user
        $optional = $this->auth->attempt($body['username'], $body['password']);

        // user is authenticated
        if ($optional->isSuccess()) {
            $data = [
                'token' => $this->auth->generateToken($body),
                'user' => Converter::convert($optional->getData())
            ];
            return $response->withJson($data);
        } else {
            // user isn't authenticated
            return $response->withJson(['errors' => ['authentication' => ['Username or password is incorrect']]], 403);
        }
    }

}