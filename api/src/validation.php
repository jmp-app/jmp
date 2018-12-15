<?php

use Respect\Validation\Validator as v;

return [
    'login' => [
        'username' => v::notEmpty()->noWhitespace()->length(1, 101),
        'password' => v::notEmpty()->length(6, 255),
    ],
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