<?php


namespace JMP\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;

class ValidationChecker
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        /**
         * @var Response
         */
        $response = $next($request, $response);

        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson([
                'errors' => $errors
            ], 400);
        } else {
            return $response;
        }

    }


}