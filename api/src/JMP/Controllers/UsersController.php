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

    public function listUsers(Request $request, Response $response): Response
    {
        $group =$request->getQueryParam('group');
        $users =$this->userService->getUsers(empty($group) ? null : $group);
        return $response->withJson(Converter::convertArray($users));
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


    /**
     * Change tha password of a user
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function changePassword(Request $request, Response $response) {
        $user = $this->auth->requestUser($request);

        if ($user->isFailure()) {
            // There has to be always a logged in user that accesses this
            return $response->withStatus(500);
        }

        $user = new User($user->getData());

        $password = $request->getParsedBodyParam('password');
        $newPassword = $request->getParsedBodyParam('newPassword');

        $passwordCheck = $this->auth->attempt($user->username, $password);
        if ($passwordCheck->isFailure()) {
            return $response->withJson([
                'errors' => [
                    'password' => 'The password is not correct'
                ]
            ], 400);
        }

        if (!$this->userService->changePassword($user->id, $newPassword)) {
            return $response->withStatus(500);
        }

        // password change was true but user changed the password > set back to false
        if ($user->passwordChange) {
            // TODO: Update user and set password change to false
        }

        return $response->withJson([
            'success' => true
        ]);

    }

}