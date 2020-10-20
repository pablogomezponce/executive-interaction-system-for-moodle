<?php

namespace TFG\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class IndexController
{
    /** @var ContainerInterface */
    private $container;

    /**
     * HelloController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $dbSettings = $container->get('sql');

        $this->sql = new \PDO('mysql:host='. $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
       $params = [
           'title' => 'EIStudy',
       ];


       $stmt = $this->sql->prepare("SELECT course.fullname, course.id, r.shortname
FROM mdl_role_assignments AS ra
    LEFT JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid
    LEFT JOIN mdl_role AS r ON ra.roleid = r.id
    LEFT JOIN mdl_context AS c ON c.id = ra.contextid
    LEFT JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id
    JOIN mdl_course AS course ON course.id = e.courseid

WHERE ue.userid = '14992'");

       $stmt->execute();
       $results = $stmt->fetchAll();

       return $this->container->get('view')->render($response, 'publicHome.twig', $params);
    }
}


