<?php


namespace jmp\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;

class CORSMiddleware
{
    /**
     * @var array
     */
    private $corsSettings;

    /**
     * CORSMiddleware constructor.
     * @param array $corsSettings
     */
    public function __construct(array $corsSettings)
    {
        $this->corsSettings = $corsSettings;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if ($request->isOptions() === false) {
            $response = $next($request, $response);
        }
        return $response
            ->withHeader('Access-Control-Allow-Origin', $this->corsSettings['origins'])
            ->withHeader('Access-Control-Allow-Headers', $this->corsSettings['headers'])
            ->withHeader('Access-Control-Allow-Methods', $this->corsSettings['methods']);
    }
}
