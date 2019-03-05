<?php

namespace JMP\Controllers;

use JMP\Models\User;
use JMP\Services\Auth;
use JMP\Services\EventService;
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
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->eventService = $container->get('eventService');
        $this->logger = $container->get('logger');
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
            return $response->withStatus(403);
        }

        // if limit and offset are not set do not use pagination
        if (empty($request->getQueryParam('limit')) && empty($request->getQueryParam('offset'))) {
            $arguments = $this->fetchArgs($request->getQueryParams(), $user->isAdmin);
            try {
                $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventType($arguments['group'], $arguments['eventType'], $arguments['all'], $arguments['elapsed'], $user));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                return $response->withStatus(500);
            }
            return $response->withJson($events);
        } else {
            $arguments = $this->fetchArgsWithPagination($request->getQueryParams(), $user->isAdmin);

            if (is_null($arguments['limit'])) {
                // no limit, just use offset
                try {
                    $events = Converter::convertArray($this->eventService->getEventByGroupAndEventWithOffset($arguments['group'],
                        $arguments['eventType'], $arguments['all'], $arguments['elapsed'], $user, $arguments['offset']));
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    return $response->withStatus(500);
                }
            } else {
                try {
                    $events = Converter::convertArray($this->eventService->getEventsByGroupAndEventTypeWithPagination($arguments['group'],
                        $arguments['eventType'], $arguments['limit'], $arguments['all'], $arguments['elapsed'], $user, $arguments['offset']));
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    return $response->withStatus(500);
                }
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
        try {
            $optional = $this->eventService->getEventById($args['id']);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $response->withStatus(500);
        }
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

}
