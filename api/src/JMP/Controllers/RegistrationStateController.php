<?php

namespace JMP\Controllers;

use JMP\Services\RegistrationStateService;
use JMP\Utils\Converter;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationStateController
{
    /**
     * @var RegistrationStateService
     */
    private $registrationStateService;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * RegistrationStateController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->registrationStateService = $container->get('registrationStateService');
        $this->logger = $container->get('logger');
    }

    public function getAllRegStates(Request $request, Response $response): Response
    {
        $optional = $this->registrationStateService->getAllRegStates();
        if ($optional->isFailure()) {
            $this->logger->addError('Failed to get all registration states');
            return $response->withStatus(500);
        }
        return $response->withJson(Converter::convertArray($optional->getData()));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getRegistrationStateById(Request $request, Response $response, array $args): Response
    {
        $id = $args['registrationStateId'];
        $optional = $this->registrationStateService->getRegistrationStateById($id);

        if ($optional->isFailure()) {
            return $response->withStatus(404);
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }
    }

}
