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


    public function listEvents(Request $request, Response $response, array $args)
    {
        // TODO: use reactions library from aarone
        if ($this->auth->requestUser($request) === false) {
            return $response->withStatus(401);
        }

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(['errors' => $errors])->withStatus(400);
        }

        if ($pagination = $this->extendedValidation($request->getQueryParams()) === false) {
            return $response->withJson([
                'errors' => [
                    'query' => [
                        'message' => "Incorrect parameter combination: limit or offset are either both or not required",
                        'params' => $request->getQueryParams()
                    ]
                ]
            ])->withStatus(400);
        }

        if ($pagination) {
            $args = $this->fetchArgsWithPagination($request->getQueryParams());
            return $response->withJson((array)$this->eventService->getEventsByGroupAndEventTypeWithPagination($args['groupId'], $args['eventTypeId'], $args['limit'], $args['offset']));
        } else {
            $args = $this->fetchArgs($request->getQueryParams());
            return $response->withJson((array)$this->eventService->getEventsByGroupAndEventType($args['groupId'], $args['eventTypeId']));
        }
    }

    private function extendedValidation(array $query)
    {
        if (isset($query['limit'])) {
            if (isset($query['offset'])) {
                return true;
            }
        }

        if (!isset($query['limit'])) {
            if (!isset($query['offset'])) {
                return true;
            }
        }
        return false;
    }

    private function fetchArgsWithPagination(array $params)
    {
        return [
            'groupId' => isset($params['group']) ? (int)$params['group'] : null,
            'eventTypeId' => isset($params['eventType']) ? (int)$params['eventType'] : null,
            'limit' => (int)$params['limit'],
            'offset' => (int)$params['offset']
        ];
    }

    private function fetchArgs(array $params)
    {
        return [
            'groupId' => isset($params['groupId']) ? (int)$params['groupId'] : null,
            'eventTypeId' => isset($params['eventTypeId']) ? (int)$params['eventTypeId'] : null,
        ];
    }

}