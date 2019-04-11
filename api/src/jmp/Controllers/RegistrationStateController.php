<?php

namespace jmp\Controllers;

use jmp\Models\RegistrationState;
use jmp\Services\RegistrationStateService;
use jmp\Utils\Converter;
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

    public function deleteRegistrationState(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        if ($this->registrationStateService->registrationStateExists($id) === false) {
            return $response->withStatus(404);
        }

        $success = $this->registrationStateService->deleteRegistrationState($id);

        if ($success === false) {
            $this->logger->error('Failed to delete registration state with the id ' . $id . '.');
            return $response->withStatus(500);
        }

        return $response->withStatus(204);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createRegistrationState(Request $request, Response $response): Response
    {
        $registrationState = new RegistrationState($request->getParsedBody());

        $optional = $this->registrationStateService->createRegistrationState($registrationState);

        if ($optional->isFailure()) {
            $this->logger->error('Failed to create registration state with the fields: {' . Converter::convert($registrationState) . '}');
            return $response->withStatus(500);
        }

        return $response->withJson(Converter::convert($optional->getData()));
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
        $id = $args['id'];
        $optional = $this->registrationStateService->getRegistrationStateById($id);

        if ($optional->isFailure()) {
            return $response->withStatus(404);
        } else {
            return $response->withJson(Converter::convert($optional->getData()));
        }
    }

}
