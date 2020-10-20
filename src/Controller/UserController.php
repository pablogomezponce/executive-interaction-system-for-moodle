<?php


namespace TFG\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserController
{
    private $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function login(RequestInterface $request, ResponseInterface $response, array $args)
    {
        return $this->container->get('view')->render($response, 'logIn.twig');
    }
}