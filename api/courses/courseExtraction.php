<?php


namespace API\courses;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class courseExtraction
{
    private $container;
    private $sql;
    private $indicators;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host=' . $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }

    public function getCourses(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $userid = $_SESSION['user']['id'];

        $stmt = $this->sql->prepare("SELECT course.fullname, course.id, r.shortname FROM role_assignments AS ra
                                                    LEFT JOIN user_enrolments AS ue ON ra.userid = ue.userid
                                                    LEFT JOIN role AS r ON ra.roleid = r.id
                                                    LEFT JOIN context AS c ON c.id = ra.contextid
                                                    LEFT JOIN enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id
                                                    JOIN course AS course ON course.id = e.courseid
                                               WHERE ue.userid = $userid");

        if($stmt->execute())
        {
            $results = $stmt->fetchAll();
            $dict = array();
            foreach ($results as $result)
            {
                $dict[$result['id']] = array('name'=>$result['fullname'], 'role'=>$result['shortname'], 'courseid'=>$result['id']);
            }

            $_SESSION['courseList'] = $dict;
            $response = $response->withStatus(200);
            $response = $response->getBody()->write(json_encode((array)$dict));
        } else {
            $response = $response->withStatus(300);
            $response = $response->getBody()->write(json_encode(array('status'=>false, 'reason'=>'server failure')));
        }

        return $response;
    }



    public function getCourseDetails(RequestInterface $request, ResponseInterface $response, array $args)
    {
        if (isset($_SESSION['courseList'][$args['courseid']]))
        {
            $response = $response->getBody()->write(json_encode($_SESSION['courseList'][$args['courseid']]));
        } else {
            $response = $response->withStatus(400, 'You are not allowed to see that');
        }

        return $response;

    }
}