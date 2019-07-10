<?php

namespace jmp\Controllers;

use Exception;
use jmp\Services\EventService;
use jmp\Services\RegistrationService;
use jmp\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationsController
{
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var RegistrationService
     */
    private $registrationService;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->eventService = $container->get('eventService');
        $this->logger = $container->get('logger');
        $this->registrationService = $container->get('registrationService');
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws Exception
     */
    public function getRegistrations(Request $request, Response $response, array $args): Response
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

        $optional = $this->registrationService->getExtendedRegistrationByEventId($eventId);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to get registrations of the event ' . $eventId . '.');
            return $response->withStatus(500);
        }

        $result['registrations'] = $optional->getData();

        return $response->withJson(Converter::convertArray($result));
    }
}
