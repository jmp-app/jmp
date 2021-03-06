<?php


namespace jmp\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;

class ValidationErrorResponseBuilder
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if ($request->getAttribute('has_errors')) {
            $errors = $request->getAttribute('errors');
            return $response->withJson(
                [
                    'errors' => $errors
                ], 400);
        } else {
            return $next($request, $response);
        }

    }


}