<?php


namespace jmp\Controllers;

use jmp\Models\Group;
use jmp\Models\Presence;
use jmp\Models\User;
use jmp\Services\EventService;
use jmp\Services\GroupService;
use jmp\Services\PresenceService;
use jmp\Services\UserService;
use jmp\Services\ValidationService;
use jmp\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class PresenceController
{

    /**
     * @var PresenceService
     */
    private $presenceService;
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
     * @var EventService
     */
    private $eventService;

    /**
     * @var GroupService
     */
    private $groupService;

    /**
     * @var ValidationService
     */
    private $validationService;

    /**
     * RegistrationStateController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->presenceService = $container->get('presenceService');
        $this->userService = $container->get('userService');
        $this->eventService = $container->get('eventService');
        $this->groupService = $container->get('groupService');
        $this->validationService = $container->get('validationService');
        $this->user = $container->get('user');
        $this->logger = $container->get('logger');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updatePresence(Request $request, Response $response, array $args): Response
    {
        $presence = new Presence([
            'event' => $args['eventId'],
            'user' => $args['userId'],
            'auditor' => $args['auditorId'],
            'hasAttended' => $request->getParsedBodyParam('hasAttended')
        ]);

        $optional = $this->presenceService->getPresenceByIds($presence->event, $presence->user, $presence->auditor);
        if ($optional->isFailure()) {
            return $response->withStatus(404);
        }

        $optional = $this->presenceService->updatePresence($presence);

        if ($optional->isFailure()) {
            $this->logger->error('Failed to update presence with the user ' . $presence->user .
                ', at the event ' . $presence->event . ' from the auditor ' . $presence->auditor);
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($optional->getData()));

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createPresence(Request $request, Response $response): Response
    {
        $presence = new Presence($request->getParsedBody());

        // Validation of user id's
        $errors = [];
        if ($this->userService->userExists($presence->user) === false) {
            $errors['user'] = 'Invald user id: ' . $presence->user;
        }
        if ($this->eventService->eventExists($presence->event) === false) {
            $errors['event'] = 'Invalid event id: ' . $presence->event;
        }

        // There are errors
        if (empty($errors) === false) {
            return $response->withJson([
                'errors' => $errors
            ], 400);
        }

        if ($this->validateUserBelongsToEvent($presence) === false) {
            return $response->withJson([
                'errors' => [
                    'presence' => 'Cant create presence of user with the id ' . $presence->user . ' at the event ' . $presence->event
                ]
            ], 400);
        }

        $presence->auditor = $this->user->id;
        $optional = $this->presenceService->createPresence($presence);
        if ($optional->isFailure()) {
            $this->logger->error('Failed to create presence with the user ' . $presence->user .
                ', at the event ' . $presence->event . ' from the auditor ' . $presence->auditor);
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($optional->getData()));
    }


    /**
     * Validates if the user can be registered to event
     * @param Presence $presence
     * @return bool
     */
    private function validateUserBelongsToEvent(Presence $presence): bool
    {
        // Get all groups of the event
        $groups = $this->groupService->getGroupsByEventId($presence->event);
        // Map Groups to groupIds
        $groupIds = array_map(function (Group $value) {
            return $value->id;
        }, $groups);
        // Check if the User has a membership in at least one of the groups
        return $this->validationService->isUserInOneOfTheGroups($groupIds, $presence->user);
    }
}

