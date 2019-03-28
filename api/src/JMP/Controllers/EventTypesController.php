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
}