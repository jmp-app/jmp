<?php

namespace JMP\Controllers;

use JMP\Services\Auth;
use JMP\Services\EventService;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class EventsController
{
    /**
     * @var \PDO
     */
    protected $db;
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
        $this->db = $container->get('database');
        $this->auth = $container->get('auth');
        $this->eventService = $container->get('eventService');
    }


    /**
     * Retrieves events from the database queried by the args.
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function listEvents(Request $request, Response $response): Response
    {
        if ($this->auth->requestUser($request)->isFailure()) {
            return $response->withStatus(403);
        }

        // check validation errors
        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors])->withStatus(400);
        }

        // if limit and offset are not set do not use pagination
        if (empty($request->getQueryParam('limit')) && empty($request->getQueryParam('offset'))) {
            $arguments = $this->fetchArgs($request->getQueryParams());
            $events = (array)$this->eventService->getEventsByGroupAndEventType($arguments['group'], $arguments['eventType']);
            return $response->withJson($events);
        } else {
            $arguments = $this->fetchArgsWithPagination($request->getQueryParams());

            if (is_null($arguments['limit'])) {
                // no limit, just use offset
                $events = (array)$this->eventService->getEventByGroupAndEventWithOffset($arguments['group'],
                    $arguments['eventType'], $arguments['offset']);
            } else {
                $events = (array)$this->eventService->getEventsByGroupAndEventTypeWithPagination($arguments['group'],
                    $arguments['eventType'], $arguments['limit'], $arguments['offset']);
            }

            return $response->withJson($events);
        }


    }

    private function fetchArgsWithPagination(array $params): array
    {
        return [
            'group' => isset($params['group']) ? (int)$params['group'] : null,
            'eventType' => isset($params['eventType']) ? (int)$params['eventType'] : null,
            'limit' => isset($params['limit']) ? (int)$params['limit'] : null,
            'offset' => (int)$params['offset']
        ];
    }

    private function fetchArgs(array $params): array
    {
        return [
            'group' => isset($params['group']) ? (int)$params['group'] : null,
            'eventType' => isset($params['eventType']) ? (int)$params['eventType'] : null,
        ];
    }

}