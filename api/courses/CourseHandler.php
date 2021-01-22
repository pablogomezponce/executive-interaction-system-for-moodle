<?php


namespace API\courses;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CourseHandler
 * This class retrieves the list of courses and roles which relates to the user logged.
 * It also offers the course fullname.
 * @package API\courses
 */

class CourseHandler
{
    private $container;
    private $sql;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host=' . $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }

    /**
     * Static method to search all courses where the user in session is enrolled.
     * @param ContainerInterface $c
     * @return bool
     */
    public static function addCoursesToSession(ContainerInterface $c)
    {
        $userid = $_SESSION['user']['id'];

        $dbSettings = $c->get('sql');
        $sql = new \PDO('mysql:host=' . $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);

        $stmt = $sql->prepare("SELECT course.fullname, course.id, r.shortname FROM role_assignments AS ra
                                                    LEFT JOIN user_enrolments AS ue ON ra.userid = ue.userid
                                                    LEFT JOIN role AS r ON ra.roleid = r.id
                                                    LEFT JOIN context AS c ON c.id = ra.contextid
                                                    LEFT JOIN enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id
                                                    JOIN course AS course ON course.id = e.courseid
                                               WHERE ue.userid = $userid");

        if ($stmt->execute()) {
            $results = $stmt->fetchAll();
            $dict = array();
            foreach ($results as $result) {
                $dict[$result['id']] = array('name' => $result['fullname'], 'role' => $result['shortname'], 'courseid' => $result['id']);
            }
            $_SESSION['courseList'] = $dict;
            return true;
        } else {
            return false;
        }

    }

    /**
     * This function offers the list of courses where the user is enroled, specifying its role: student or teacher.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return int
     */
    public function getCourses(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $isCourseInSession = CourseHandler::addCoursesToSession($this->container);

        if ($isCourseInSession){
            $response = $response->withStatus(200);
            $response = $response->getBody()->write(json_encode((array)$_SESSION['courseList']));
        } else {
            $response = $response->withStatus(300);
            $response = $response->getBody()->write(json_encode(array('status'=>false, 'reason'=>'server failure')));
        }

        return $response;
    }


    /**
     * Offers basic details for a course, as its id and its fullname.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return int
     */
    public function getCourseDetails(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $courseid = $args['courseid'];
        
        if(isset($_SESSION['courseList'][$courseid]))
        {
            $stmt = $this->sql->prepare("SELECT course.fullname, course.id FROM course
                                               WHERE course.id = $courseid");

            if ($stmt->execute())
            {
                $results = $stmt->fetch();

                $response = $response->withStatus(200);
                $response = $response->getBody()->write(json_encode((array)$results));
            } else {
                $response = $response->withStatus(300);
                $response = $response->getBody()->write(json_encode(array('status'=>false, 'reason'=>'server failure')));
            }

        } else {
            $response = $response->withStatus(400);
            $response = $response->getBody()->write(json_encode(array('status'=>false, 'reason'=>'Not related to course')));
        }
        

        return $response;


    }
}