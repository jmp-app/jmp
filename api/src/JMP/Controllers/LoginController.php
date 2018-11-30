<?php

namespace JMP\Controllers;

use Interop\Container\ContainerInterface;


/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 29.11.18
 * Time: 19:17
 */
class LoginController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function login($request, $response, $args)
    {
        // your code
        // to access items in the container... $this->container->get('');
        return $response->withJson(['username' => 'dominik']);
    }

    public function contact($request, $response, $args)
    {
        // your code
        // to access items in the container... $this->container->get('');
        return $response->withJson(['username' => 'dominik']);
    }
}