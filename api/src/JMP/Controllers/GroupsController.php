<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;
use JMP\Services\GroupService;
use JMP\Services\MembershipService;
use JMP\Utils\Converter;
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
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->groupService = $container->get('groupService');
        $this->membershipService = $container->get('membershipService');
    }

    /**
     * Returns all existing groups
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listGroups(Request $request, Response $response): Response
    {
        $groups = Converter::convertArray($this->groupService->getAllGroups());
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

        $group = $this->groupService->createGroup($name);
        return $response->withJson(Converter::convert($group));
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
        $this->groupService->deleteGroup($id);
        return $response->withJson([
            'success' => 'Deleted group with id "' . $id . '"'
        ]);
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
        $group = $this->groupService->getGroupById($id);

        if ($group->isFailure()) {
            return $this->groupIdNotAvailable($response, $id);
        }

        return $response->withJson(Converter::convert($group->getData()));
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
            return $this->groupIdNotAvailable($response, $id);
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

        $group = $this->groupService->updateGroup($id, $newName);
        return $response->withJson(Converter::convert($group));

    }

    /**
     * Join users to a (existing) group
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function joinGroup(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $users = $request->getParsedBodyParam('users');

        // Check if group exists
        if (!$this->groupService->groupExists($id)) {
            return $this->groupIdNotAvailable($response, $id);
        }

        // Add users to the group
        $optional = $this->membershipService->addUsersToGroup($id, $users);
        if ($optional->isFailure()) {
            // operation failed
            return $response->withStatus(500);
        }

        // Retrieve the updated group and return it
        $group = $this->groupService->getGroupById($id);
        return $response->withJson(Converter::convert($group->getData()));
    }

    /**
     * Leave user from a (existing) group
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function leaveGroup(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];
        $users = $request->getParsedBodyParam('users');

        // Check if group exists
        if (!$this->groupService->groupExists($id)) {
            return $this->groupIdNotAvailable($response, $id);
        }

        // Remove users from the group
        $optional = $this->membershipService->removeUsersFromGroup($id, $users);
        if ($optional->isFailure()) {
            // operation failed
            return $response->withStatus(500);
        }

        // Retrieve the updated group and return it
        $group = $this->groupService->getGroupById($id);
        return $response->withJson(Converter::convert($group->getData()));
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
