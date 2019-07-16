<?php

namespace jmp\Controllers;

use Exception;
use jmp\Models\Presence;
use jmp\Models\User;
use jmp\Services\EventService;
use jmp\Services\PresencesService;
use jmp\Services\UserService;
use jmp\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class PresencesController
{
    /**
     * @var User
     */
    protected $user;
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var PresencesService
     */
    private $presencesService;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * PresenceController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->eventService = $container->get('eventService');
        $this->logger = $container->get('logger');
        $this->presencesService = $container->get('presencesService');
        $this->userService = $container->get('userService');
        $this->user = $container->get('user');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function deletePresences(Request $request, Response $response, array $args): Response
    {
        $eventId = $args['id'];
        $users = $request->getParsedBodyParam('users');

        if ($this->eventService->eventExists($eventId) === false) {
            return $response->withStatus(404);
        }

        list($existingUsers, $notExistingUsers) = $this->userService->groupUsersByValidity($users);

        if (!empty($notExistingUsers)) {
            return $response->withJson([
                'errors' => [
                    'invalidUsers' => $notExistingUsers
                ]
            ], 400);
        }

        $presences = [];
        foreach ($users as $user) {
            $presence = [];
            $presence['event'] = $eventId;
            $presence['auditor'] = $this->user->id;
            $presence['user'] =
            $presences[] = new Presence($presence);
        }

        $success = $this->presencesService->deletePresences($presences);
        if ($success === false) {
            $this->logger->error('Failed to delete presences of the event ' . $eventId . '.');
            return $response->withStatus(500);
        }

        return $response->withStatus(204);
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function updatePresences(Request $request, Response $response, array $args): Response
    {
        $eventId = $args['id'];
        $presences = $request->getParsedBodyParam('presences');

        if ($this->eventService->eventExists($eventId) === false) {
            return $response->withStatus(404);
        }

        $users = [];
        foreach ($presences as $presence) {
            $users[] = $presence['user'];
        }

        list($existingUsers, $notExistingUsers) = $this->userService->groupUsersByValidity($users);

        if (!empty($notExistingUsers)) {
            return $response->withJson([
                'errors' => [
                    'invalidUsers' => $notExistingUsers
                ]
            ], 400);
        }

        $optional = $this->eventService->getEventById($eventId);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to get event with the id ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result = [
            'event' => $optional->getData()
        ];

        foreach ($presences as $key => $presence) {
            $presence['event'] = $eventId;
            $presence['auditor'] = $this->user->id;
            $presences[$key] = new Presence($presence);
        }

        $optional = $this->presencesService->updatePresences($eventId, $presences);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to update presences of the event ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result['presences'] = $optional->getData();

        return $response->withJson(Converter::convertArray($result));
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function getPresences(Request $request, Response $response, array $args): Response
    {
        $eventId = $args['id'];

        if ($this->eventService->eventExists($eventId) === false) {
            return $response->withStatus(404);
        }

        $optional = $this->eventService->getEventById($eventId);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to get event with the id ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result = [
            'event' => $optional->getData()
        ];

        $optional = $this->presencesService->getExtendedPresencesByEventId($eventId);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to get presences of the event ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result['presences'] = $optional->getData();

        return $response->withJson(Converter::convertArray($result));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function createPresences(Request $request, Response $response, array $args): Response
    {
        $eventId = $args['id'];
        $presences = $request->getParsedBodyParam('presences');

        if ($this->eventService->eventExists($eventId) === false) {
            return $response->withStatus(404);
        }

        $users = [];
        foreach ($presences as $presence) {
            $users[] = $presence['user'];
        }

        list($existingUsers, $notExistingUsers) = $this->userService->groupUsersByValidity($users);

        if (!empty($notExistingUsers)) {
            return $response->withJson([
                'errors' => [
                    'invalidUsers' => $notExistingUsers
                ]
            ], 400);
        }

        $optional = $this->eventService->getEventById($eventId);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to get event with the id ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result = [
            'event' => $optional->getData()
        ];

        foreach ($presences as $key => $presence) {
            $presence['event'] = $eventId;
            $presence['auditor'] = $this->user->id;
            $presences[$key] = new Presence($presence);
        }

        $optional = $this->presencesService->createPresences($eventId, $presences);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to create presences of the event ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result['presences'] = $optional->getData();

        return $response->withJson(Converter::convertArray($result));
    }
}
