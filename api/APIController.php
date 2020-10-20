<?php


namespace TFG\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class APIController
{
    /**@var ContainerInterface*/
    private $container;


    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function relatedContent(Request $request, ResponseInterface $response, array $args)
    {
        return json_encode(($_POST));
    }
}