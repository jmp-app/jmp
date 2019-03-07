<?php

namespace JMP\Controllers;

use JMP\Models\User;
use JMP\Services\Auth;
use JMP\Services\EventService;
use JMP\Services\EventTypeService;
use JMP\Services\GroupService;
use JMP\Services\RegistrationStateService;
use JMP\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class EventsController
{
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var User
     */
    private $user;
    /**
     * @var EventTypeService
     */
    private $eventTypeService;
    /**
     * @var GroupService
     */
    private $groupService;

    /**
     * @var RegistrationStateService
     */
    private $registrationStateService;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->eventService = $container->get('eventService');
        $this->logger = $container->get('logger');
        $this->user = $container->get('user');
        $this->eventTypeService = $container->get('eventTypeService');
        $this->groupService = $container->get('groupService');
        $this->registrationStateService = $container->get('registrationStateService');
    }


    /**
     * Retrieves events from the database queried by the args.
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function listEvents(Request $request, Response $response): Response
    {
        if ((bool)$this->user->isAdmin !== true && isset($request->getQueryParams()['all']) === true) {
            return $response->withStatus(403);
        }

        // if limit and offset are not set do not use pagination
        if (empty($request->getQueryParam('limit')) && empty($request->getQueryParam('offset'))) {
            $arguments = $this->fetchArgs($request->getQueryParams(), $this->user->isAdmin);
            $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventType($arguments['group'], $arguments['eventType'], $arguments['all'], $arguments['elapsed'], $this->user));
            return $response->withJson($events);
        } else {
            $arguments = $this->fetchArgsWithPagination($request->getQueryParams(), $this->user->isAdmin);

            if (is_null($arguments['limit'])) {
                // no limit, just use offset
                $events = Converter::convertArray($this->eventService->getEventByGroupAndEventWithOffset($arguments['group'],
                    $arguments['eventType'], $arguments['all'], $arguments['elapsed'], $this->user, $arguments['offset']));
            } else {
                $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventTypeWithPagination($arguments['group'],
                    $arguments['eventType'], $arguments['limit'], $arguments['all'], $arguments['elapsed'], $this->user, $arguments['offset']));
            }

            return $response->withJson($events);
        }


    }

    /**
     * Retrieves event from the database queried by the id in args.
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Exception
     */
    public function getEventById(Request $request, Response $response, array $args): Response
    {
        $optional = $this->eventService->getEventById($args['id'], $this->user);

        if ($optional->isFailure()) {
            return $response->withStatus(404);
        } else {
            $event = Converter::convert($optional->getData());
            return $response->withJson($event);
        }
    }

    /**
     * Creates a new Event
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function createEvent(Request $request, Response $response): Response
    {
        $params = $request->getParsedBody();

        // Validate the input
        $errors = [];
        $errors = $this->validateEventType($params, $errors);
        $errors = $this->validateGroups($params, $errors);
        $errors = $this->validateRegistrationState($params, $errors);

        if (empty($errors) === false) {
            return $response->withJson([
                'errors' => $errors
            ], 400);
        }

        $optional = $this->eventService->createEvent($params);
        if ($optional->isFailure()) {
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($optional->getData()));
    }

    /**
     * @param array $params
     * @param bool $isAdmin
     * @return array
     */
    private function fetchArgsWithPagination(array $params, bool $isAdmin): array
    {
        return [
            'group' => isset($params['group']) ? (int)$params['group'] : null,
            'eventType' => isset($params['eventType']) ? (int)$params['eventType'] : null,
            'limit' => isset($params['limit']) ? (int)$params['limit'] : null,
            'offset' => (int)$params['offset'],
            'all' => isset($params['all']) && $isAdmin === true ? (bool)$params['all'] : false,
            'elapsed' => isset($params['elapsed']) ? (bool)$params['elapsed'] : false
        ];
    }

    /**
     * @param array $params
     * @param bool $isAdmin
     * @return array
     */
    private function fetchArgs(array $params, bool $isAdmin): array
    {
        return [
            'group' => isset($params['group']) ? (int)$params['group'] : null,
            'eventType' => isset($params['eventType']) ? (int)$params['eventType'] : null,
            'all' => isset($params['all']) && $isAdmin === true ? (bool)$params['all'] : false,
            'elapsed' => isset($params['elapsed']) ? (bool)$params['elapsed'] : false
        ];
    }

    /**
     * @param $params
     * @param array $errors
     * @return array
     */
    private function validateEventType($params, array $errors): array
    {
        if ($this->eventTypeService->eventTypeExists($params['eventType']) === false) {
            $errors['eventType'] = 'An event type with the id ' . $params['eventType'] . ' doesnt exist';
        }
        return $errors;
    }

    /**
     * @param $params
     * @param array $errors
     * @return array
     */
    private function validateGroups($params, array $errors): array
    {
        $groupsWhichNotExists = [];
        foreach ($params['groups'] as $groupId) {
            if ($this->groupService->groupExists($groupId) === false) {
                array_push($groupsWhichNotExists, $groupId);
            }
        }
        if (empty($groupsWhichNotExists) === false) {
            $errors['groups'] = 'The following groups dont exist: [' . implode(', ', $groupsWhichNotExists) . ']';
        }
        return $errors;
    }

    /**
     * @param $params
     * @param array $errors
     * @return array
     */
    private function validateRegistrationState($params, array $errors): array
    {
        if ($this->registrationStateService->registrationStateExists($params['defaultRegistrationState']) === false) {
            $errors['defaultRegistrationState'] = 'A egistration state with the id ' . $params['defaultRegistrationState'] . ' doesnt exist';
        }
        return $errors;
    }

}
