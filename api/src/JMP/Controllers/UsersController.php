<?php


namespace JMP\Controllers;


use Interop\Container\ContainerInterface;
use JMP\Services\Auth;
use JMP\Services\UserService;
use Slim\Http\Request;
use Slim\Http\Response;

class UsersController
{

    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->userService = $container->get('userService');
    }

    public function createUser(Request $request, Response $response)
    {
        if ($this->auth->requestAdmin($request)->isFailure()) {
            return $response->withStatus(403);
        }

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors], 400);
        }

        // TODO (dominik): extended validation -> username valid?


        return $response->withJson($request->getParsedBody());
    }

}