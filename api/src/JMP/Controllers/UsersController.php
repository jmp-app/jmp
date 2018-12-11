<?php


namespace JMP\Controllers;


use Interop\Container\ContainerInterface;
use JMP\Models\User;
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
     * @var callable
     */
    private $nullFilter;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->userService = $container->get('userService');
        $this->nullFilter = $container->get('nullFilter');
    }

    public function createUser(Request $request, Response $response): Response
    {
        if ($this->auth->requestAdmin($request)->isFailure()) {
            return $response->withStatus(403);
        }

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors], 400);
        }

        $user = $request->getParsedBody();

        if ($this->userService->isUsernameUnique($user['username'])) {
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

            $user = $this->userService->createUser(new User($user));

            return $response->withJson(array_filter((array)$user, $this->nullFilter));
        } else {
            return $response->withJson([
                'errors' => [
                    'User' => 'A user with the username ' . $user['username'] . ' already exists'
                ]
            ], 400);
        }
    }


}