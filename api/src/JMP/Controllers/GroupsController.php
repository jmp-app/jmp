<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;
use JMP\Services\GroupService;
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
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->groupService = $container->get('groupService');
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
            return $response->withJson([
                'errors' => [
                    'name' => 'A group with the name "' . $name . '"" already exists'
                ]
            ], 400);
        }

        $group = $this->groupService->createGroup($name);
        return $response->withJson(Converter::convert($group));
    }

}
