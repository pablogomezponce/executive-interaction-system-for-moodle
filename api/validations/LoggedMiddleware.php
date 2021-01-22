<?php


namespace API\validations;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LoggedMiddleware+
 * This class is a middleware defined to prevent calls to the API when there is no session started. This middleware
 * enables the statfulness for the API.
 * @package API\validations
 */
class LoggedMiddleware
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
        header("Content-Type: text/html;charset=utf-8");
        //$response->getBody()->write(json_encode(array('status'=>false, 'reason'=>'Session not started, login first')));
        $response = $response->withStatus(400,  'Debes acceder a tu cuenta');
        return $response;
    }
}