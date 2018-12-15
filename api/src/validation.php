<?php

use Respect\Validation\Validator as v;

$container = $app->getContainer();

$container['validation'] = function (\Psr\Container\ContainerInterface $container) {
    return [
        'login' => [
            'username' => v::notEmpty()->noWhitespace()->length(1, 101),
            'password' => v::notEmpty()->length(6, 255),
        ],
        'loginTranslation' => function ($message) {
            $messages = [
                '{{name}} must have a length between {{minValue}} and {{maxValue}}' => 'Must have a length between {{minValue}} and {{maxValue}}',
                '{{name}} must not be empty' => 'Must not be empty'
            ];
            return $messages[$message];
        },
        'listEvents' => [
            'group' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'eventType' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'limit' => v::optional(v::noWhitespace()->numeric()->min(0)),
            'offset' => v::optional(v::noWhitespace()->numeric()->min(0)),
        ],
        'createUser' => [
            'username' => v::notEmpty()->noWhitespace()->length(1, 101),
            'lastname' => v::optional(v::notEmpty()->noWhitespace()->length(1, 51)),
            'firstname' => v::optional(v::notEmpty()->noWhitespace()->length(1, 51)),
            'password' => v::notEmpty()->length(1, 256),
            'email' => v::optional(v::notEmpty()->length(1, 256)::email())
        ]
    ];
};