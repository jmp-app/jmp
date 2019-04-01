<?php

namespace JMP\Controllers;

use JMP\Models\EventType;
use JMP\Models\User;
use JMP\Services\EventTypeService;
use JMP\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class EventTypesController
{
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
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get('logger');
        $this->user = $container->get('user');
        $this->eventTypeService = $container->get('eventTypeService');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateEventType(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $params = $request->getParams();

        if ($this->eventTypeService->eventTypeExists($id) === false) {
            return $response->withStatus(404);
        }

        $eventType = new EventType($params);
        $eventType->id = $id;

        $optional = $this->eventTypeService->updateEventType($eventType);

        if ($optional->isFailure()) {
            $this->logger->error('Failed to update event type with the id ' . $id . '.');
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($optional->getData()));
    }

    /**
     * Delete an event type
     * The event type is only deleted when it currently isn't in use.
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteEventType(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        if ($this->eventTypeService->eventTypeExists($id) === false) {
            return $response->withStatus(404);
        }

        if ($this->eventTypeService->isEventTypeUsed($id) === true) {
            return $response->withJson([
                'errors' => [
                    'eventType' => 'The event type with the id ' . $id . ' is currently in use by some events and can therefore not be deleted'
                ]
            ], 400);
        }

        if ($this->eventTypeService->deleteEventType($id) === false) {
            $this->logger->error('Failed to delete event type with the id ' . $id . '.');
            return $response->withStatus(500);
        }

        return $response->withStatus(204);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getEventTypeById(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        $optional = $this->eventTypeService->getEventTypeById($id);
        if ($optional->isFailure()) {
            return $response->withStatus(404);
        }

        return $response->withJson(Converter::convert($optional->getData()));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createEventType(Request $request, Response $response): Response
    {
        $eventType = new EventType($request->getParsedBody());

        $optional = $this->eventTypeService->createEventType($eventType);

        if ($optional->isFailure()) {
            $this->logger->error('Failed to create new event type with the following fields: {' . json_encode(Converter::convert($eventType)) . '}');
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($optional->getData()));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listEventTypes(Request $request, Response $response): Response
    {
        $optional = $this->eventTypeService->getAllEventTypes();

        if ($optional->isFailure()) {
            $this->logger->error('Failed to list all event types');
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convertArray($optional->getData()));
    }
}