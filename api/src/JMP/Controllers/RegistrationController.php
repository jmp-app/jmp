<?php

namespace JMP\Controllers;

use JMP\Models\Registration;
use JMP\Models\RegistrationState;
use JMP\Models\User;
use JMP\Services\Auth;
use JMP\Services\EventService;
use JMP\Services\RegistrationService;
use JMP\Services\RegistrationStateService;
use JMP\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationController
{
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var RegistrationService
     */
    private $registrationService;
    /**
     * @var RegistrationStateService
     */
    private $registrationStateService;
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
     * RegistrationController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->registrationService = $container->get('registrationService');
        $this->registrationStateService = $container->get('registrationStateService');
        $this->eventService = $container->get('eventService');
        $this->logger = $container->get('logger');
        $this->user = $container->get('user');
    }

    public function getRegistrationByEventIdAndUserId(Request $request, Response $response, array $args): Response
    {
        $optional = $this->registrationService->getRegistrationByUserIdAndEventId($args['userId'], $args['eventId']);
        if ($optional->isSuccess()) {
            return $response->withJson(Converter::convert($optional->getData()));
        } else {
            return $response->withStatus(404);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function createRegistration(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody();

        $registration = new Registration($parsedBody);
        $registration->registrationState = new RegistrationState([
            'id' => $parsedBody['registrationState']
        ]);

        $optional = $this->registrationService->getRegistrationByUserIdAndEventId($registration->userId, $registration->eventId);

        if ($optional->isSuccess()) {
            //Registration already exists
            return $response->withJson(Converter::convert($optional->getData()));
        }

        $event = $this->eventService->getEventById($registration->eventId, $this->user);

        if ($event->isFailure()) {
            return $this->getBadRequestResponse($response, "Invalid parameters");
        }

        if (!isset($parsedBody['registrationState'])) {
            $registration->registrationState->id = $event->getData()->defaultRegistrationState->id;
        }

        $optional = $this->registrationService->createRegistration($registration);

        if ($optional->isFailure()) {
            return $this->getBadRequestResponse($response, "Invalid parameters");
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }

    }

    public function updateRegistration(Request $request, Response $response, $args): Response
    {
        $eventId = $args['eventId'];
        $userId = $args['userId'];
        $newRegistrationState = $request->getParsedBodyParam('registrationState');
        $newReason = $request->getParsedBodyParam('reason');

        $registration = $this->registrationService->getRegistrationByUserIdAndEventId($userId, $eventId);

        if ($registration->isFailure()) {
            return $response->withStatus(404);
        }

        $newRegistrationState = $this->registrationStateService->getRegistrationTypeById($newRegistrationState);
        if ($newRegistrationState->isFailure()) {
            return $this->getBadRequestResponse($response, "Invalid RegistrationState");
        }

        $newRegistrationState = $newRegistrationState->getData();

        if ($newRegistrationState->reasonRequired) {
            if (empty($newReason)) {
                return $this->getBadRequestResponse($response, "Reason is required");
            }
        }

        $updatedRegistration = new Registration([
            "eventId" => $eventId,
            "userId" => $userId,
            "reason" => $newReason

        ]);
        $updatedRegistration->registrationState = new RegistrationState([
            "id" => $newRegistrationState->id
        ]);

        $optional = $this->registrationService->updateRegistration($updatedRegistration);

        if ($optional->isFailure()) {
            return $this->getBadRequestResponse($response, "Invalid parameters");
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }


    }

    /**
     * @param Response $response
     * @param $message
     * @return Response
     */
    public function getBadRequestResponse(Response $response, $message): Response
    {
        return $response->withJson([
            "errors" => [
                $message
            ]
        ], 400);
    }
}