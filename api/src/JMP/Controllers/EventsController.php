<?php

namespace JMP\Controllers;

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
        if ($this->auth->requestUser($request)->isFailure()) {
            return $response->withStatus(403);
        }

        // check validation errors
        if ($request->getAttribute('has_errors')) {
            return $response;
        }
        // if limit and offset are not set do not use pagination
        if (empty($request->getQueryParam('limit')) && empty($request->getQueryParam('offset'))) {
            $arguments = $this->fetchArgs($request->getQueryParams());
            $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventType($arguments['group'], $arguments['eventType']));
            return $response->withJson($events);
        } else {
            $arguments = $this->fetchArgsWithPagination($request->getQueryParams());

            if (is_null($arguments['limit'])) {
                // no limit, just use offset
                $events = Converter::convertArray($this->eventService->getEventByGroupAndEventWithOffset($arguments['group'],
                    $arguments['eventType'], $arguments['offset']));
            } else {
                $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventTypeWithPagination($arguments['group'],
                    $arguments['eventType'], $arguments['limit'], $arguments['offset']));
            }

            return $response->withJson($events);
        }


    }

    /**
     * @param array $params
     * @return array
     */
    private function fetchArgsWithPagination(array $params): array
    {
        return [
            'group' => isset($params['group']) ? (int)$params['group'] : null,
            'eventType' => isset($params['eventType']) ? (int)$params['eventType'] : null,
            'limit' => isset($params['limit']) ? (int)$params['limit'] : null,
            'offset' => (int)$params['offset']
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    private function fetchArgs(array $params): array
    {
        return [
            'group' => isset($params['group']) ? (int)$params['group'] : null,
            'eventType' => isset($params['eventType']) ? (int)$params['eventType'] : null,
        ];
    }

}