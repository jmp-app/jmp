<?php

namespace JMP\Controllers;

use JMP\Models\Event;
use JMP\Models\Group;
use JMP\Models\Registration;
use JMP\Models\RegistrationState;
use JMP\Models\User;
use JMP\Services\Auth;
use JMP\Services\EventService;
use JMP\Services\GroupService;
use JMP\Services\RegistrationService;
use JMP\Services\RegistrationStateService;
use JMP\Services\UserService;
use JMP\Services\ValidationService;
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
     * @var UserService
     */
    private $userService;
    /**
     * @var GroupService
     */
    private $groupService;
    /**
     * @var ValidationService
     */
    private $validationService;

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
        $this->userService = $container->get('userService');
        $this->logger = $container->get('logger');
        $this->user = $container->get('user');
        $this->groupService = $container->get('groupService');
        $this->validationService = $container->get('validationService');
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
        // Parse input
        $parsedBody = $request->getParsedBody();
        $registration = new Registration($parsedBody);
        $registration->registrationState = new RegistrationState([
            'id' => $parsedBody['registrationState']
        ]);

        // Check if registration already exists -> return registration
        $optional = $this->registrationService->getRegistrationByUserIdAndEventId($registration->userId, $registration->eventId);
        if ($optional->isSuccess()) {
            //Registration already exists
            return $response->withJson(Converter::convert($optional->getData()));
        }

        $errors = [];
        // Validate Event
        $errors = $this->validateEvent($registration->eventId, $errors);
        // Validate User
        $errors = $this->validateUser($registration->userId, $errors);
        // Return bad request error when one of the above failed
        if (empty($errors) === false) {
            return $this->getBadRequestResponse($response, $errors);
        }

        // Validate User belongs to the event
        $errors = $this->validateUserBelongsToEvent($registration);
        if (empty($errors) === false) {
            return $this->getBadRequestResponse($response, $errors);
        }

        $optional = $this->eventService->getEventById($registration->eventId);
        if ($optional->isFailure()) {
            $message = 'Cant register user with the id ' . $registration->userId . ' to the event ' . $registration->eventId;
            return $this->getBadRequestResponseWithKey($response, 'registration', $message);
        }

        // Have to check if the id is falsy, because default value of an int is 0 and not null
        if (isset($registration->registrationState->id) == false) {
            /** @var Event $event */
            $event = $optional->getData();
            $registration->registrationState = $event->defaultRegistrationState;
        } else {
            // Validate registrationState
            $optional = $this->registrationStateService->getRegistrationStateById($registration->registrationState->id);
            if ($optional->isSuccess()) {
                $registration->registrationState = $optional->getData();
            } else {
                $message = 'A registrationState with the id ' . $registration->registrationState->id . ' doesnt exist';
                return $this->getBadRequestResponseWithKey($response, 'registrationState', $message);
            }
        }

        // Validate reason if required
        if ($registration->registrationState->reasonRequired == true && isset($registration->reason) == false) {
            $message = 'A reason is required but not delivered';
            return $this->getBadRequestResponseWithKey($response, 'reason', $message);
        }

        // Create registration
        $optional = $this->registrationService->createRegistration($registration);
        if ($optional->isFailure()) {
            return $response->withStatus(500);
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function updateRegistration(Request $request, Response $response, $args): Response
    {
        $eventId = $args['eventId'];
        $userId = $args['userId'];
        $newRegistrationState = $request->getParsedBodyParam('registrationState');
        $newReason = $request->getParsedBodyParam('reason');

        $optional = $this->registrationService->getRegistrationByUserIdAndEventId($userId, $eventId);
        if ($optional->isFailure()) {
            return $response->withStatus(404);
        }
        /** @var Registration $oldRegistration */
        $oldRegistration = $optional->getData();

        if (!$newRegistrationState) {
            $newRegistrationState = $oldRegistration->registrationState;
        } else {
            $optional = $this->registrationStateService->getRegistrationStateById($newRegistrationState);
            if ($optional->isFailure()) {
                $message = 'A registrationState with the id ' . (int)$request->getParsedBodyParam('registrationState') . ' doesnt exist';
                return $this->getBadRequestResponseWithKey($response, "registrationState", $message);
            }
            $newRegistrationState = $optional->getData();
        }

        if ($newRegistrationState->reasonRequired) {
            if (empty($newReason)) {
                $message = 'A reason is required but not delivered';
                return $this->getBadRequestResponseWithKey($response, 'reason', $message);
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
            return $response->withStatus(500);
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }


    }

    /**
     * @param Response $response
     * @param string $key
     * @param $message
     * @return Response
     */
    public function getBadRequestResponseWithKey(Response $response, string $key, $message): Response
    {
        return $response->withJson([
            'errors' => [
                $key => $message
            ]
        ], 400);
    }

    /**
     * @param Response $response
     * @param $message
     * @return Response
     */
    public function getBadRequestResponse(Response $response, $message): Response
    {
        return $response->withJson([
            "errors" => $message
        ], 400);
    }

    /**
     * Checks if the event exists
     * @param int $eventId
     * @param array $errors
     * @return array
     * @throws \Exception
     */
    private function validateEvent(int $eventId, array $errors): array
    {
        if ($this->eventService->eventExists($eventId) === false) {
            $errors['eventId'] = 'An Event with the id ' . $eventId . ' doesnt exist';
        }
        return $errors;
    }

    /**
     * @param int $userId
     * @param $errors
     * @return array
     */
    private function validateUser(int $userId, $errors): array
    {
        if ($this->userService->userExists($userId) === false) {
            $errors['userId'] = 'An User with the id ' . $userId . ' doesnt exist';
        }
        return $errors;
    }

    /**
     * Validates if the user can be registered to event
     * @param Registration $registration
     * @return array
     */
    private function validateUserBelongsToEvent(Registration $registration): array
    {
        $error = [];
        // Get all groups of the event
        $groups = $this->groupService->getGroupsByEventId($registration->eventId);
        // Map Groups to groupIds
        $groupIds = array_map(function (Group $value) {
            return $value->id;
        }, $groups);
        // Check if the User has a membership in at least one of the groups
        if ($this->validationService->isUserInOneOfTheGroups($groupIds, $registration->userId) === false) {
            $error['registration'] = 'Cant register user with the id ' . $registration->userId . ' to the event ' . $registration->eventId;
        }
        return $error;
    }

}
