<?php

namespace jmp\Controllers;

use Exception;
use Interop\Container\ContainerInterface;
use jmp\Services\GroupService;
use jmp\Services\MembershipService;
use jmp\Services\UserService;
use jmp\Utils\Converter;
use Monolog\Logger;
use Slim\Http\Request;
use Slim\Http\Response;

class GroupsController
{

    /**
     * @var GroupService
     */
    private $groupService;

    /**
     * @var MembershipService
     */
    private $membershipService;
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->groupService = $container->get('groupService');
        $this->membershipService = $container->get('membershipService');
        $this->userService = $container->get('userService');
        $this->logger = $container->get('logger');
    }

    /**
     * Returns all existing groups
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listGroups(Request $request, Response $response): Response
    {
        $groups = Converter::convertArray($this->groupService->getAllGroups(true));
        return $response->withJson($groups);
    }

    /**
     * Creates a new group
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createGroup(Request $request, Response $response): Response
    {
        $name = $request->getParsedBodyParam('name');

        if (!$this->groupService->isGroupNameUnique($name)) {
            return $this->groupNameNotAvailable($response, $name);
        }

        $optional = $this->groupService->createGroup($name);

        if ($optional->isSuccess()) {
            return $response->withJson(Converter::convert($optional->getData()));
        } else {
            return $response->withStatus(404);
        }
    }

    /**
     * Deletes a group
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function deleteGroup(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        if ($this->groupService->groupExists($id) === false) {
            return $response->withStatus(404);
        }

        if ($this->groupService->deleteGroup($id) === false) {
            $this->logger->error('Failed to delete group with the id ' . $id . '.');
            return $response->withStatus(500);
        }
        return $response->withStatus(204);
    }

    /**
     * Retrieve group by its id
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function getGroupById(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $optional = $this->groupService->getGroupById($id);

        if ($optional->isFailure()) {
            return $response->withStatus(404);
        }

        return $response->withJson(Converter::convert($optional->getData()));
    }

    /**
     * Update group data
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function updateGroup(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $group = $this->groupService->getGroupById($id);

        // Check if group exists
        if ($group->isFailure()) {
            return $response->withStatus(404);
        }
        $group = $group->getData();


        $newName = $request->getParsedBodyParam('name');

        // check if a new name is specified and is different from the current name
        if (is_null($newName) || $newName === $group->name) {
            return $response->withJson(Converter::convert($group));
        }

        // check if new name is valid
        if (!$this->groupService->isGroupNameUnique($newName)) {
            return $this->groupNameNotAvailable($response, $newName);
        }

        $optional = $this->groupService->updateGroup($id, $newName);

        if ($optional->isSuccess()) {
            return $response->withJson(Converter::convert($optional->getData()));
        } else {
            return $response->withStatus(404);
        }
    }

    /**
     * Join users to a (existing) group
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws Exception
     */
    public function joinGroup(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $users = $request->getParsedBodyParam('users');

        // Check if group exists
        if (!$this->groupService->groupExists($id)) {
            return $response->withStatus(404);
        }

        list($usersToJoin, $usersNotToJoin) = $this->userService->groupUsersByValidity($users);

        // Add users to the group
        $successful = $this->membershipService->addUsersToGroup($id, $usersToJoin);
        if ($successful === false) {
            // operation failed
            $this->logger->addError('Failed to join users to a group. Group: "' . $id . '" Users: "' . $usersToJoin . '"');
            return $response->withStatus(500);
        }

        // Retrieve the updated group and return it
        $optionalGroup = $this->groupService->getGroupById($id);

        $responseArray = [
            'group' => Converter::convert($optionalGroup->getData()),
        ];

        // If user cant be joined to a group, a error message is appended
        if (!empty($usersNotToJoin)) {
            $responseArray['errors'] = ['invalidUsers' => $usersNotToJoin];
        }
        return $response->withJson($responseArray);
    }

    /**
     * Leave user from a (existing) group
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws Exception
     */
    public function leaveGroup(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $users = $request->getParsedBodyParam('users');

        // Check if group exists
        if (!$this->groupService->groupExists($id)) {
            return $response->withStatus(404);
        }

        // Remove users from the group
        $successful = $this->membershipService->removeUsersFromGroup($id, $users);
        if ($successful === false) {
            // operation failed
            $this->logger->addError('Failed to remove users from a group. Group: "' . $id . '" Users: "' . $users . '"');
            return $response->withStatus(500);
        }

        // Retrieve the updated group and return it
        $optional = $this->groupService->getGroupById($id);
        if ($optional->isFailure()) {
            $this->logger->addError('Failed to query group after removing members. Group: "' . $id . '" Users: "' . $users . '"');
            return $response->withStatus(500);
        } else {
            return $response->withJson(Converter::convert($optional->getData()), 200);
        }
    }

    /**
     * Responds with an error that the group id could not be found
     * @param Response $response
     * @param int $id
     * @return Response
     */
    private function groupIdNotAvailable(Response $response, int $id): Response
    {
        return $response->withJson([
            'errors' => [
                'id' => 'The specified id "' . $id . '"does not exist'
            ]
        ], 404);
    }

    /**
     * Responds with an error that the group name is already taken
     * @param Response $response
     * @param string $name
     * @return Response
     */
    private function groupNameNotAvailable(Response $response, string $name): Response
    {
        return $response->withJson([
            'errors' => [
                'name' => 'A group with the name "' . $name . '"" already exists'
            ]
        ], 400);
    }

}
