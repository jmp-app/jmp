<?php


namespace JMP\Middleware;


use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AppErrorHandler
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var bool
     */
    private $debug;

    /**
     * AppErrorHandler constructor.
     * @param ContainerInterface $container
     * @param bool $debug
     */
    public function __construct(ContainerInterface $container, bool $debug)
    {
        $this->logger = $container->get('logger');
        $this->debug = $debug;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception $exception
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception): Response
    {
        $this->logger->addError($exception->getMessage());
        return $response
            ->withStatus(500)
            ->withJson([
                'errors' => $this->getErrorMessage($exception)
            ]);
    }

    /**
     * @param \Exception $exception
     * @return \Exception|string
     */
    private function getErrorMessage(\Exception $exception)
    {
        if ($this->debug === false) {
            return 'Internal Server Error';
        } else {
            return $exception;
        }
    }


}