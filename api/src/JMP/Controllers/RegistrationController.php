<?php

namespace JMP\Controllers;

use JMP\Models\Registration;
use JMP\Models\RegistrationState;
use JMP\Services\Auth;
use JMP\Services\RegistrationService;
use JMP\Services\RegistrationStateService;
use JMP\Utils\Converter;
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
     * RegistrationController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->registrationService = $container->get('registrationService');
        $this->registrationStateService = $container->get('registrationStateService');
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

        $optional = $this->registrationService->createRegistration($registration);

        if ($optional->isFailure()) {
            return $response->withJson([
                "errors" => [
                    "Invalid parameters"
                ]
            ], 400);
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
            return $response->withJson([
                "errors" => [
                    "Invalid RegistrationState"
                ]
            ], 400);
        }

        $newRegistrationState = $newRegistrationState->getData();

        if ($newRegistrationState->reasonRequired) {
            if (empty($newReason)) {
                return $response->withJson([
                    "errors" => [
                        "Reason is required"
                    ]
                ], 400);
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
            return $response->withJson([
                "errors" => [
                    "Invalid parameters"
                ]
            ], 400);
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }


    }
}