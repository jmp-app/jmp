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
        list($eventId, $users) = $this->parseParamsWithUsers($request, $args);

        if ($this->eventExists($eventId)) {
            return $this->getNotFoundErrorResponse($response);
        }

        $notExistingUsers = $this->getInvalidUsers($users);

        if (!empty($notExistingUsers)) {
            return $this->getInvalidUsersErrorResponse($response, $notExistingUsers);
        }

        $presences = $this->getPresencesFromUsersArray($users, $eventId);

        $success = $this->presencesService->deletePresences($presences);
        if ($success === false) {
            $errorMessage = 'Failed to delete presences of the event ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
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
        list($eventId, $presences) = $this->parseParamsWithPresences($request, $args);

        if ($this->eventExists($eventId)) {
            return $this->getNotFoundErrorResponse($response);
        }

        $notExistingUsers = $this->getInvalidUsersOfPresences($presences);

        if (!empty($notExistingUsers)) {
            return $this->getInvalidUsersErrorResponse($response, $notExistingUsers);
        }

        $optional = $this->eventService->getEventById($eventId);
        if ($optional->isFailure()) {
            $errorMessage = 'Failed to get event with the id ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
        }

        $result = [
            'event' => $optional->getData()
        ];

        $presences = $this->getPresencesFromArray($presences, $eventId);

        $optional = $this->presencesService->updatePresences($eventId, $presences);
        if ($optional->isFailure()) {
            $errorMessage = 'Failed to update presences of the event ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
        }

        $result['presences'] = $optional->getData();

        return $this->getResultResponse($response, $result);
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

        if ($this->eventExists($eventId)) {
            return $this->getNotFoundErrorResponse($response);
        }

        $optional = $this->eventService->getEventById($eventId);
        if ($optional->isFailure()) {
            $errorMessage = 'Failed to get event with the id ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
        }

        $result = [
            'event' => $optional->getData()
        ];

        $optional = $this->presencesService->getExtendedPresencesByEventId($eventId);
        if ($optional->isFailure()) {
            $errorMessage = 'Failed to get presences of the event ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
        }

        $result['presences'] = $optional->getData();

        return $this->getResultResponse($response, $result);
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
        list($eventId, $presences) = $this->parseParamsWithPresences($request, $args);

        if ($this->eventExists($eventId)) {
            return $this->getNotFoundErrorResponse($response);
        }

        $notExistingUsers = $this->getInvalidUsersOfPresences($presences);

        if (!empty($notExistingUsers)) {
            return $this->getInvalidUsersErrorResponse($response, $notExistingUsers);
        }

        $optional = $this->eventService->getEventById($eventId);
        if ($optional->isFailure()) {
            $errorMessage = 'Failed to get event with the id ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
        }

        $result = [
            'event' => $optional->getData()
        ];

        $presences = $this->getPresencesFromArray($presences, $eventId);

        $optional = $this->presencesService->createPresences($eventId, $presences);
        if ($optional->isFailure()) {
            $errorMessage = 'Failed to create presences of the event ' . $eventId . '.';
            return $this->getInternalServerErrorResponse($response, $errorMessage);
        }

        $result['presences'] = $optional->getData();

        return $this->getResultResponse($response, $result);
    }

    /**
     * @param $presences
     * @return array
     */
    private function getInvalidUsersOfPresences(array $presences): array
    {
        $users = [];
        foreach ($presences as $presence) {
            $users[] = $presence['user'];
        }

        return $this->getInvalidUsers($users);
    }

    /**
     * @param array $users
     * @return array
     */
    private function getInvalidUsers(array $users): array
    {
        list($existingUsers, $notExistingUsers) = $this->userService->groupUsersByValidity($users);
        return $notExistingUsers;
    }

    /**
     * @param Response $response
     * @param array $notExistingUsers
     * @return Response
     */
    private function getInvalidUsersErrorResponse(Response $response, array $notExistingUsers): Response
    {
        return $response->withJson([
            'errors' => [
                'invalidUsers' => $notExistingUsers
            ]
        ], 400);
    }

    /**
     * @param Request $request
     * @param array $args
     * @return array
     */
    private function parseParamsWithUsers(Request $request, array $args): array
    {
        $eventId = $args['id'];
        $users = $request->getParsedBodyParam('users');
        return array($eventId, $users);
    }

    /**
     * @param Request $request
     * @param array $args
     * @return array
     */
    private function parseParamsWithPresences(Request $request, array $args): array
    {
        $eventId = $args['id'];
        $presences = $request->getParsedBodyParam('presences');
        return array($eventId, $presences);
    }

    /**
     * @param Response $response
     * @param string $errorMessage
     * @return Response
     */
    private function getInternalServerErrorResponse(Response $response, string $errorMessage): Response
    {
        $this->logger->error($errorMessage);
        return $response->withStatus(500);
    }

    /**
     * @param $eventId
     * @return bool
     */
    private function eventExists($eventId): bool
    {
        return $this->eventService->eventExists($eventId) === false;
    }

    /**
     * @param Response $response
     * @return Response
     */
    private function getNotFoundErrorResponse(Response $response): Response
    {
        return $response->withStatus(404);
    }

    /**
     * @param $presences
     * @param $eventId
     * @return mixed
     */
    private function getPresencesFromArray($presences, $eventId): array
    {
        foreach ($presences as $key => $presence) {
            $presence['event'] = $eventId;
            $presence['auditor'] = $this->user->id;
            $presences[$key] = new Presence($presence);
        }
        return $presences;
    }

    /**
     * @param $users
     * @param $eventId
     * @return array
     */
    private function getPresencesFromUsersArray($users, $eventId): array
    {
        $presences = [];
        foreach ($users as $user) {
            $presence = [];
            $presence['event'] = $eventId;
            $presence['auditor'] = $this->user->id;
            $presence['user'] = $user;
            $presences[] = new Presence($presence);
        }
        return $presences;
    }

    /**
     * @param Response $response
     * @param array $result
     * @return Response
     */
    private function getResultResponse(Response $response, array $result): Response
    {
        return $response->withJson(Converter::convertArray($result));
    }
}
