<?php


namespace JMP\Controllers;


use Interop\Container\ContainerInterface;
use JMP\Models\User;
use JMP\Services\Auth;
use JMP\Services\UserService;
use JMP\Utils\Converter;
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

    /**
     * Get current logged in user
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCurrentUser(Request $request, Response $response): Response
    {
        $user = $this->auth->requestUser($request);

        if ($user->isFailure()) {
            // There has to be always a logged in user that accesses this
            return $response->withStatus(500);
        }

        $user = new User($user->getData());

        return $response->withJson(Converter::convert($user));
    }

    /**
     * Returns all users
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listUsers(Request $request, Response $response): Response
    {
        $group =$request->getQueryParam('group');
        $users =$this->userService->getUsers(empty($group) ? null : $group);
        return $response->withJson(Converter::convertArray($users));
    }

    /**
     * Update data of a user
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $updates = $request->getParsedBody();

        if (!$this->userService->userExists($id)) {
            return $response->withJson([
                'errors' => [
                    'id' => 'The specified id "' . $id . '"does not exist'
                ]
            ], 404);
        }

        $updatedUser = $this->userService->updateUser($id, $updates);

        if ($updatedUser->isFailure()) {
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($updatedUser->getData()));
    }

    /**
     * Returns the user with the given id or a 404
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUser(Request $request, Response $response, array $args): Response
    {
        $userId = $args['id'];

        $optional = $this->userService->getUserByUserId($userId);

        if ($optional->isSuccess()) {
            return $response->withJson(Converter::convert($optional->getData()));
        } else {
            return $response->withStatus(404);
        }
    }

    /**
     * Returns the user or an error
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createUser(Request $request, Response $response): Response
    {
        $user = $request->getParsedBody();

        // check if the username is already used by an other user
        if ($this->userService->isUsernameUnique($user['username'])) {
            return $this->usernameAvailable($response, $user);
        } else {
            return $this->usernameNotAvailable($response, $user);
        }
    }

    /**
     * Create the error response if a username is already in use
     * @param Response $response
     * @param $user
     * @return Response
     */
    private function usernameNotAvailable(Response $response, $user): Response
    {
        return $response->withJson([
            'errors' => [
                'User' => 'A user with the username ' . $user['username'] . ' already exists'
            ]
        ], 400);
    }

    /**
     * Create the response if a user can be created successfully
     * @param Response $response
     * @param $user
     * @return Response
     */
    private function usernameAvailable(Response $response, $user): Response
    {
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

        $user = $this->userService->createUser(new User($user));

        return $response->withJson(Converter::convert($user));
    }

}