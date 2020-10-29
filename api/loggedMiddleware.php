<?php


namespace API;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class loggedMiddleware
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!empty($_SESSION['user']))
        {
            $response = $next($request, $response);
            return $response;
        }
        //$response->getBody()->write(json_encode(array('status'=>false, 'reason'=>'Session not started, login first')));
        $response = $response->withStatus(400, 'Session not started, login first!');
        return $response;
    }
}