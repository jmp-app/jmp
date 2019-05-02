<?php

use DavidePastore\Slim\Validation\Validation;
use jmp\Controllers\EventsController;
use jmp\Controllers\EventTypesController;
use jmp\Controllers\GroupsController;
use jmp\Controllers\LoginController;
use jmp\Controllers\RegistrationController;
use jmp\Controllers\RegistrationStateController;
use jmp\Controllers\UsersController;
use jmp\Middleware\AuthenticationMiddleware;
use jmp\Middleware\CORSMiddleware;
use jmp\Middleware\ValidationErrorResponseBuilder;
use jmp\Utils\PermissionLevel;
use Psr\Container\ContainerInterface;
use Tuupola\Middleware\JwtAuthentication;

/** @var $app Slim\App */
/** @var ContainerInterface $container */
/** @var $jwtMiddleware JwtAuthentication */

$container = $app->getContainer();
$jwtMiddleware = $container['jwt'];

// CORS
$app->add(new CORSMiddleware($container['settings']['cors']));

// API Routes - version 1
$app->group('/v1', function () use ($container, $jwtMiddleware) {
    /** @var $this Slim\App */

    // Login
    $this->group('/login', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });

        $this->post('', LoginController::class . ':login')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation(
                $container['validation']['login'],
                $container['validation']['loginTranslation']
            ))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::OPEN));
    });

    // Events
    $this->group('/events', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });

        $this->get('', EventsController::class . ':listEvents')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['listEvents']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->post('', EventsController::class . ':createEvent')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createEvent']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->group('/{id:[0-9]+}', function () use ($container, $jwtMiddleware) {
            /** @var $this Slim\App */
            $this->options('', function () {
            });

            $this->get('', EventsController::class . ':getEventById')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);

            $this->put('', EventsController::class . ':updateEvent')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($container['validation']['updateEvent']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->delete('', EventsController::class . ':deleteEvent')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

        });
    });

    // Registration
    $this->group('/registration', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });

        $this->post('', RegistrationController::class . ':createRegistration')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['createRegistration']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->group('/{eventId:[0-9]+}/{userId:[0-9]+}', function () use ($container, $jwtMiddleware) {
            /** @var $this Slim\App */
            $this->options('', function () {
            });

            $this->get('', RegistrationController::class . ':getRegistrationByEventIdAndUserId')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);

            $this->put('', RegistrationController::class . ':updateRegistration')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($this->getContainer()['validation']['updateRegistration']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);

            $this->delete('', RegistrationController::class . ':deleteRegistration')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);

        });
    });

    // Registration-State
    $this->group('/registration-state', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });

        $this->get('', RegistrationStateController::class . ':getAllRegStates')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['getAllRegStates']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->post('', RegistrationStateController::class . ':createRegistrationState')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($this->getContainer()['validation']['createRegistrationState']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->group('/{id:[0-9]+}', function () use ($container, $jwtMiddleware) {
            /** @var $this Slim\App */
            $this->options('', function () {
            });

            $this->put('', RegistrationStateController::class . ':updateRegistrationState')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($this->getContainer()['validation']['updateRegistrationState']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->get('', RegistrationStateController::class . ':getRegistrationStateById')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);


            $this->delete('', RegistrationStateController::class . ':deleteRegistrationState')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);
        });
    });

    // User
    $this->group('/user', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });


        $this->get('', UsersController::class . ':getCurrentUser')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->options('/change-password', function () {
        });

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
        $this->options('', function () {
        });

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

        $this->group('/{id:[0-9]+}', function () use ($container, $jwtMiddleware) {
            /** @var $this Slim\App */
            $this->options('', function () {
            });

            $this->put('', UsersController::class . ':updateUser')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($container['validation']['updateUser']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->delete('', UsersController::class . ':deleteUser')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->get('', UsersController::class . ':getUser')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);
        });
    });

    // Groups
    $this->group('/groups', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });

        $this->post('', GroupsController::class . ':createGroup')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createGroup']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->get('', GroupsController::class . ':listGroups')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->group('/{id:[0-9]+}', function () use ($container, $jwtMiddleware) {
            /** @var $this Slim\App */
            $this->options('', function () {
            });

            $this->get('', GroupsController::class . ':getGroupById')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);

            $this->put('', GroupsController::class . ':updateGroup')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($container['validation']['updateGroup']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->delete('', GroupsController::class . ':deleteGroup')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->options('/join', function () {
            });

            $this->post('/join', GroupsController::class . ':joinGroup')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($container['validation']['userIdsArray']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->options('/leave', function () {
            });

            $this->delete('/leave', GroupsController::class . ':leaveGroup')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($container['validation']['userIdsArray']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);
        });
    });

    $this->group('/event-types', function () use ($container, $jwtMiddleware) {
        /** @var $this Slim\App */
        $this->options('', function () {
        });

        $this->post('', EventTypesController::class . ':createEventType')
            ->add(new ValidationErrorResponseBuilder())
            ->add(new Validation($container['validation']['createEventType']))
            ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
            ->add($jwtMiddleware);

        $this->get('', EventTypesController::class . ':listEventTypes')
            ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
            ->add($jwtMiddleware);

        $this->group('/{id:[0-9]+}', function () use ($container, $jwtMiddleware) {
            /** @var $this Slim\App */
            $this->options('', function () {
            });

            $this->get('', EventTypesController::class . ':getEventTypeById')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
                ->add($jwtMiddleware);

            $this->put('', EventTypesController::class . ':updateEventType')
                ->add(new ValidationErrorResponseBuilder())
                ->add(new Validation($container['validation']['updateEventType']))
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);

            $this->delete('', EventTypesController::class . ':deleteEventType')
                ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
                ->add($jwtMiddleware);
        });
    });
});
