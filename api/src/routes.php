<?php

use DavidePastore\Slim\Validation\Validation;
use JMP\Controllers\EventsController;
use JMP\Controllers\GroupsController;
use JMP\Controllers\LoginController;
use JMP\Controllers\RegistrationController;
use JMP\Controllers\UsersController;
use JMP\Middleware\AuthenticationMiddleware;
use JMP\Middleware\ValidationErrorResponseBuilder;
use JMP\Utils\PermissionLevel;

// API Routes - version 1
$app->group('/v1', function () {

    $container = $this->getContainer();
    $jwtMiddleware = $container->get('jwt');

    $this->post('/login', LoginController::class . ':login')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation(
            $container['validation']['login'],
            $container['validation']['loginTranslation']
        ))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::OPEN));

    $this->get('/events', EventsController::class . ':listEvents')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['listEvents']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->get('/events/{id:[0-9]+}', EventsController::class . ':getEventById')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['getEventById']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->get('/registration/{eventId:[0-9]+}/{userId:[0-9]+}', RegistrationController::class . ':getRegistrationByEventIdAndUserId')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['getRegistrationByEventIdAndUserId']))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->post('/registration', RegistrationController::class . ':createRegistration')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($this->getContainer()['validation']['createRegistration']))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->put('/registration/{eventId}/{userId}', RegistrationController::class . ':updateRegistration')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($this->getContainer()['validation']['updateRegistration']))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->get('/registration-state', \JMP\Controllers\RegistrationStateController::class . ':getAllRegStates')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($this->getContainer()['validation']['getAllRegStates']))
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->get('/user', UsersController::class . ':getCurrentUser')
        ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
        ->add($jwtMiddleware);
  
      $this->put('/user/change-password', UsersController::class . ':changePassword')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation(
            $container['validation']['changePassword'],
            $container['validation']['loginTranslation']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))

    $this->post('/users', UsersController::class . ':createUser')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['createUser']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->get('/users', UsersController::class . ':listUsers')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['listUsers']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->put('/users/{id:[0-9]+}', UsersController::class . ':updateUser')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['updateUser']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->delete('/users/{id:[0-9]+}', UsersController::class . ':deleteUser')
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->get('/users/{id:[0-9]+}', UsersController::class . ':getUser')
        ->add(new AuthenticationMiddleware($container, \JMP\Utils\PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->post('/groups', GroupsController::class . ':createGroup')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['createGroup']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->get('/groups', GroupsController::class . ':listGroups')
        ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->get('/groups/{id:[0-9]+}', GroupsController::class . ':getGroupById')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['getGroupById']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::USER))
        ->add($jwtMiddleware);

    $this->put('/groups/{id:[0-9]+}', GroupsController::class . ':updateGroup')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['updateGroup']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->delete('/groups/{id:[0-9]+}', GroupsController::class . ':deleteGroup')
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->post('/groups/{id:[0-9]+}/join', GroupsController::class . ':joinGroup')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['userIdsArray']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

    $this->delete('/groups/{id:[0-9]+}/leave', GroupsController::class . ':leaveGroup')
        ->add(new ValidationErrorResponseBuilder())
        ->add(new Validation($container['validation']['userIdsArray']))
        ->add(new AuthenticationMiddleware($container, PermissionLevel::ADMIN))
        ->add($jwtMiddleware);

});
