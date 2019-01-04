<?php

use Respect\Validation\Validator as v;

$container = $app->getContainer();

$container['validation'] = function () {
    return [
        'login' => [
            'username' => v::notEmpty()->noWhitespace()->length(1, 101),
            'password' => v::notEmpty()->length(8, 255),
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
            'password' => v::notEmpty()->length(1, 256),
            'email' => v::optional(v::notEmpty()->length(1, 256)::email()),
            'isAdmin' => v::optional(v::boolType())
        ],
        'listUsers' => [
            'username' => v::optional(v::notEmpty()->noWhitespace()->numeric()),
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
    ];
};
