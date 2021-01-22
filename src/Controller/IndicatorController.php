<?php


namespace TFG\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndicatorController
{

    private $container;
    private $sql;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host='. $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }

    public function defineParams(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $params = array('courseid'=>$args['courseid'], 'title'=>'Extraer indicadores | EIStudy');
        return $this->container->get('view')->render($response, 'indicatorform.twig',$params);
    }
}