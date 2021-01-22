<?php


namespace TFG\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CourseController
{

    private $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args)
    {
        return $this->container->get('view')->render($response, 'courselist.twig', ['title' => 'Asignaturas | Eistudy']);
    }
}