<?php

namespace JMP\Controllers;

use JMP\Models\Registration;
use JMP\Models\RegistrationState;
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
        $optional = $this->registrationService->getRegistrationByUserIdAndEventId($args['userId'], $args['eventId']);
        if ($optional->isSuccess()) {
            return $response->withJson(Converter::convert($optional->getData()));
        } else {
            return $response->withStatus(404);
        }
    }

    public function createRegistration(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody();

        $registration = new Registration($parsedBody);
        $registration->registrationState = new RegistrationState($parsedBody['registrationState']);

        $optional = $this->registrationService->createRegistration($registration);

        if ($optional->isFailure()) {
            return $response->withJson([
                "errors" => "Invalid parameters"
            ], 400);
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }

    }
}