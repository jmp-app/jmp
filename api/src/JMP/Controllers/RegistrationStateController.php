<?php

namespace JMP\Controllers;

use JMP\Services\Auth;
use JMP\Services\RegistrationStateService;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationStateController
{
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var RegistrationStateService
     */
    private $registrationStateService;

    /**
     * RegistrationStateController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->registrationStateService = $container->get('registrationStateService');
    }

    public function getAllRegStates(Request $request, Response $response): Response
    {
        $registrationStates = $this->registrationStateService->getAllRegStates();
        if (!empty($registrationStates)) {
            return $response->withJson($registrationStates);
        }
        return $response->withStatus(404);
    }


}
