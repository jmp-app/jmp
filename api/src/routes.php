<?php

use DavidePastore\Slim\Validation\Validation;
use JMP\Controllers\EventsController;
use JMP\Controllers\EventTypesController;
use JMP\Controllers\GroupsController;
use JMP\Controllers\LoginController;
use JMP\Controllers\RegistrationController;
use JMP\Controllers\RegistrationStateController;
use JMP\Controllers\UsersController;
use JMP\Middleware\AuthenticationMiddleware;
use JMP\Middleware\ValidationErrorResponseBuilder;
use JMP\Utils\PermissionLevel;
use Psr\Container\ContainerInterface;
use Tuupola\Middleware\JwtAuthentication;

/** @var $app Slim\App */

// API Routes - version 1
$app->group('/v1', function () {
    /** @var $this Slim\App */
    /** @var ContainerInterface $container */
    /** @var $jwtMiddleware JwtAuthentication */

    $container = $this->getContainer();
    $jwtMiddleware = $container['jwt'];

    $this->post('/login', LoginController::class . ':login')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation(
            $container['validation']['login'],
            $container['validation']['loginTranslation']
        ))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::OPEN));

    // Events
    $this->group('/events', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->get('', EventsController::class . ':listEvents')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['listEvents']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->get('/{id:[0-9]+}', EventsController::class . ':getEventById')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['getEventById']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->post('', EventsController::class . ':createEvent')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createEvent']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->put('/{id:[0-9]+}', EventsController::class . ':updateEvent')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['updateEvent']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->delete('/{id:[0-9]+}', EventsController::class . ':deleteEvent')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);
    });

    // Registration
    $this->group('/registration', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */

        $this->get('/{eventId:[0-9]+}/{userId:[0-9]+}', RegistrationController::class . ':getRegistrationByEventIdAndUserId')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['getRegistrationByEventIdAndUserId']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->post('', RegistrationController::class . ':createRegistration')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['createRegistration']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->put('/{eventId}/{userId}', RegistrationController::class . ':updateRegistration')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['updateRegistration']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->delete('/{eventId}/{userId}', RegistrationController::class . ':deleteRegistration')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['deleteRegistration']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);
    });

    // Registration-State
    $this->group('/registration-state', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */

        $this->get('', RegistrationStateController::class . ':getAllRegStates')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['getAllRegStates']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->get('/{registrationStateId:[0-9]+}', RegistrationStateController::class . ':getRegistrationStateById')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['getRegistrationStateById']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);
    });

    // User
    $this->group('/user', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */

        $this->get('', UsersController::class . ':getCurrentUser')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->put('/change-password', UsersController::class . ':changePassword')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation(
                $container['validation']['changePassword'],
                $container['validation']['loginTranslation']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);
    });

    // Users
    $this->group('/users', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */

        $this->post('', UsersController::class . ':createUser')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createUser']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->get('', UsersController::class . ':listUsers')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['listUsers']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->put('/{id:[0-9]+}', UsersController::class . ':updateUser')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['updateUser']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->delete('/{id:[0-9]+}', UsersController::class . ':deleteUser')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->get('/{id:[0-9]+}', UsersController::class . ':getUser')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);
    });

    // Groups
    $this->group('/groups', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */

        $this->post('', GroupsController::class . ':createGroup')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createGroup']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->get('', GroupsController::class . ':listGroups')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->get('/{id:[0-9]+}', GroupsController::class . ':getGroupById')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['getGroupById']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->put('/{id:[0-9]+}', GroupsController::class . ':updateGroup')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['updateGroup']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->delete('/{id:[0-9]+}', GroupsController::class . ':deleteGroup')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->post('/{id:[0-9]+}/join', GroupsController::class . ':joinGroup')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['userIdsArray']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->delete('/{id:[0-9]+}/leave', GroupsController::class . ':leaveGroup')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['userIdsArray']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);
    });

    $this->group('/event-types', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */

        $this->post('', EventTypesController::class . ':createEventType')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createEventType']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->get('', EventTypesController::class . ':listEventTypes')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->get('/{id:[0-9]+}', EventTypesController::class . ':getEventTypeById')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->put('/{id:[0-9]+}', EventTypesController::class . ':updateEventType')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['updateEventType']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->delete('/{id:[0-9]+}', EventTypesController::class . ':deleteEventType')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);
    });
});
