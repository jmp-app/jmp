<?php

namespace JMP\Controllers;

use JMP\Services\Auth;
use JMP\Services\RegistrationService;
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
     * RegistrationController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->registrationService = $container->get('registrationService');
    }

    public function getRegistrationByEventIdAndUserId(Request $request, Response $response, array $args): Response
    {
        $registration = Converter::convert(
            $this->registrationService->getRegistrationByUserIdAndEventId($args['userId'], $args['eventId'])
        );
        return $response->withJson($registration);
    }
}