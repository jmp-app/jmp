<?php

use Respect\Validation\Validator as v;

/** @var $container \Psr\Container\ContainerInterface */
$container = $app->getContainer();

$container['validation'] = function () {
    return [
        'login' => [
            'username' => v::notEmpty()->noWhitespace()->length(1, 101),
            'password' => v::notEmpty()->length(8, 256),
        ],
        'loginTranslation' => function ($message) {
            $messages = [
                '{{name}} must have a length between {{minValue}} and {{maxValue}}' => 'Must have a length between {{minValue}} and {{maxValue}}',
                '{{name}} must not be empty' => 'Must not be empty',
                '{{name}} must not contain whitespace' => 'Must not contain whitespace'
            ];
            return $messages[$message];
        },
        'listEvents' => [
            'group' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'eventType' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'limit' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'offset' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'all' => v::optional(v::boolVal()),
            'elapsed' => v::optional(v::boolVal()),
        ],
        'createEvent' => [
            'title' => v::notEmpty()->length(1, 51),
            'description' => v::optional(v::notEmpty()->length(1, 256)),
            'from' => v::date('Y-m-d\TH:i'),
            'to' => v::date('Y-m-d\TH:i'),
            'place' => v::optional(v::notEmpty()->length(1, 51)),
            'eventType' => v::noWhitespace()->numeric()->min(0),
            'defaultRegistrationState' => v::noWhitespace()->numeric()->min(0),
            'groups' => v::arrayType()->notEmpty()->each(
                v::noWhitespace()->numeric()->min(0)
            )
        ],
        'getEventById' => [
            'id' => v::notEmpty()->noWhitespace()->numeric()
        ],
        'getRegistrationByEventIdAndUserId' => [
            'eventId' => v::notEmpty()->noWhitespace()->numeric(),
            'userId' => v::notEmpty()->noWhitespace()->numeric()
        ],
        'createUser' => [
            'username' => v::notEmpty()->noWhitespace()->length(1, 101),
            'lastname' => v::optional(v::notEmpty()->noWhitespace()->length(1, 51)),
            'firstname' => v::optional(v::notEmpty()->noWhitespace()->length(1, 51)),
            'password' => v::notEmpty()->length(8, 256),
            'email' => v::optional(v::notEmpty()->length(1, 256)::email()),
            'isAdmin' => v::optional(v::boolVal()),
            'passwordChange' => v::boolVal()
        ],
        'updateUser' => [
            'username' => v::optional(v::notEmpty()->noWhitespace()->length(1, 101)),
            'lastname' => v::optional(v::notEmpty()->noWhitespace()->length(1, 51)),
            'firstname' => v::optional(v::notEmpty()->noWhitespace()->length(1, 51)),
            'password' => v::optional(v::notEmpty()->length(8, 256)),
            'email' => v::optional(v::notEmpty()->length(1, 255)::email()),
            'isAdmin' => v::optional(v::boolVal()),
            'passwordChange' => v::optional(v::boolVal())
        ],
        'listUsers' => [
            'group' => v::optional(v::notEmpty()->noWhitespace()->numeric()),
        ],
        'createGroup' => [
            'name' => v::notEmpty()->length(1, 45)
        ],
        'getGroupById' => [
            'id' => v::notEmpty()->noWhitespace()->numeric()
        ],
        'updateGroup' => [
            'name' => v::optional(v::notEmpty()->length(1, 45))
        ],
        'changePassword' => [
            'password' => v::notEmpty()->length(8, 256),
            'newPassword' => v::notEmpty()->length(8, 256)
        ],
        'userIdsArray' => [
            'users' => v::arrayType()->length(1, null)->each(v::numeric())
        ],
        'createRegistration' => [
            'eventId' => v::notEmpty()->noWhitespace()->numeric(),
            'userId' => v::notEmpty()->noWhitespace()->numeric(),
            'reason' => v::optional(v::notEmpty()->length(1, 80)),
            'registrationState' => v::optional(v::notEmpty()->noWhitespace()->numeric()->min(0))
        ],
        'updateRegistration' => [
            'eventId' => v::notEmpty()->noWhitespace()->numeric()->min(0),
            'userId' => v::notEmpty()->noWhitespace()->numeric()->min(0),
            'reason' => v::optional(v::notEmpty()->length(1, 80)),
            'registrationState' => v::optional(v::notEmpty()->noWhitespace()->numeric()->min(0))
        ],
        'getRegistrationStateById' => [
            'registrationStateId' => v::notEmpty()->noWhitespace()->numeric()->min(0)
        ],
        'deleteRegistration' => [
            'eventId' => v::notEmpty()->noWhitespace()->numeric()->min(0),
            'userId' => v::notEmpty()->noWhitespace()->numeric()->min(0),
        ]
    ];
};
