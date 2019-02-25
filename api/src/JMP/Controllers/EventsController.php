<?php

namespace JMP\Controllers;

use JMP\Models\User;
use JMP\Services\Auth;
use JMP\Services\EventService;
use JMP\Utils\Converter;
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
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->eventService = $container->get('eventService');
    }


    /**
     * Retrieves events from the database queried by the args.
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listEvents(Request $request, Response $response): Response
    {
        $optional = $this->auth->requestUser($request);
        if ($optional->isFailure()) {
            return $response->withStatus(401);
        }
        /** @var User $user */
        $user = new User($optional->getData());

        if ((bool)$user->isAdmin !== true && isset($request->getQueryParams()['all']) === true) {
            return $response->withStatus(401);
        }

        // if limit and offset are not set do not use pagination
        if (empty($request->getQueryParam('limit')) && empty($request->getQueryParam('offset'))) {
            $arguments = $this->fetchArgs($request->getQueryParams(), $user->isAdmin);
            $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventType($arguments['group'], $arguments['eventType'], $arguments['all'], $user));
            return $response->withJson($events);
        } else {
            $arguments = $this->fetchArgsWithPagination($request->getQueryParams(), $user->isAdmin);

            if (is_null($arguments['limit'])) {
                // no limit, just use offset
                $events = Converter::convertArray($this->eventService->getEventByGroupAndEventWithOffset($arguments['group'],
                    $arguments['eventType'], $arguments['all'], $user, $arguments['offset']));
            } else {
                $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventTypeWithPagination($arguments['group'],
                    $arguments['eventType'], $arguments['limit'], $arguments['all'], $user, $arguments['offset']));
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
     */
    public function getEventById(Request $request, Response $response, array $args): Response
    {
        $optional = $this->eventService->getEventById($args['id']);
        if ($optional->isFailure()) {
            return $response->withStatus(404);
        } else {
            $event = Converter::convert($optional->getData());
            return $response->withJson($event);
        }
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
            'all' => isset($params['all']) && $isAdmin === true ? (bool)$params['all'] : false
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
            'all' => isset($params['all']) && $isAdmin === true ? (bool)$params['all'] : false
        ];
    }

}