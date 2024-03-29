<?php


namespace API\indicators;


use API\validations\LoggedMiddleware;
use http\Env\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This class contains the operations towards the target database to extract data.
 * Class IndicatorHandler
 * @package API\indicators
 */
class IndicatorHandler
{

    private ContainerInterface $container;
    private \PDO $sql;

    /**
     * IndicatorHandler constructor. Depends on Slim Container.
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host=' . $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
        $this->indicators = IndicatorHandler::getAllIndicators();
    }

    private static $individualTotalInteractions = ['sql' => 'SELECT count(action) as amount FROM logstore mlsl JOIN user mu ON mu.id = mlsl.userid WHERE courseid = %courseid AND userid = %userid group by userid', 'kind' => "COUNTER"];
    private static $individualRankingInteractions = ['sql' => 'select ranking.userid, ranking.times, ranking.position as amount, mu.username  from user mu join ( SELECT userid, count(action) as "times", row_number() over (order by count(action) desc) position FROM logstore mlsl where courseid = %courseid group by userid order by count(action) desc ) ranking on mu.id = ranking.userid where userid = %userid', 'kind' => 'COUNTER'];
    private static $individualAVGTimeToModify = ['sql' => 'select userid, sec_to_time(avg(time_to_sec(timediff(from_unixtime(ma.duedate), from_unixtime(mas.timemodified))))) as amount from assign ma JOIN assign_submission mas on mas.assignment = ma.id where course = %courseid   and ma.duedate - mas.timemodified > 0   and status = "submitted" and userid = %userid group by userid;', 'kind' => 'COUNTER'];
    private static $individualAmountOfNP = ['sql' => 'select userid, (select count(id) from mdl_assign where course = %courseid) - count(distinct assignment) as amount from assign_submission mas where mas.assignment in (select ma2.id from mdl_assign ma2 where course = %courseid) and userid = %userid group by userid;', 'kind' => 'COUNTER'];
    private static $individualResourcesSeen = ['sql' => "select mr.name as 'Nombre del recurso', count(mlsl.id) as 'Veces visto' from logstore mlsl     join resource mr on mr.id = mlsl.objectid     join user mu on mu.id = mlsl.userid where courseid = %courseid and component = 'mod_resource' and mlsl.userid = %userid group by userid, name order by name asc;", 'kind' => 'LIST'];
    private static $individualAmountResourcesSeen = ['sql' => "select count(mlsl.id) as 'amount' from logstore mlsl     join resource mr on mr.id = mlsl.objectid     join user mu on mu.id = mlsl.userid where courseid = %courseid and component = 'mod_resource' and mlsl.userid = %userid group by userid;", 'kind' => 'COUNTER'];
    private static $individualCourseTimesSeenAmount = ['sql' => "select rank() over (order by count(mlsl.id) desc) as '#' ,mu.username as 'Nombre de usuario', count(mlsl.id) as 'amount' from logstore mlsl join user mu on mu.id = mlsl.userid where courseid = %courseid and mlsl.eventname = '\\\\core\\\\event\\\\course_viewed' and mu.id = %userid group by mu.username order by count(mlsl.id) desc", 'kind' => 'COUNTER'];
    private static $individualURLSeen = ['sql' => "SELECT rank() over (order by count(mlsl.id) desc) as '#', name as 'Nombre del enlace', count(mlsl.id) as 'Cantidad de veces visto' FROM url murl     join logstore mlsl on mlsl.courseid = murl.course and component = 'mod_url' and murl.id = mlsl.objectid     join mdl_user mu on mu.id = mlsl.userid WHERE course = %courseid and action = 'viewed' and userid = %userid group by name;", 'kind' => 'LIST'];

    private static $grupalRankgingInteractions = ['sql' => 'select ranking.position as "#",mu.username as "Nombre de usuario", ranking.times as "Cantidad de interacciones" from user mu join ( SELECT userid, count(action) as "times", row_number() over (order by count(action) desc) position FROM logstore mlsl where courseid = %courseid group by userid order by count(action) desc ) ranking on mu.id = ranking.userid', 'kind' => 'LIST'];
    private static $grupalAVGTimeToModify = ['sql' => 'select mu.username as "Nombre de usuario",        sec_to_time(avg(time_to_sec(timediff(from_unixtime(ma.duedate), from_unixtime(mas.timemodified))))) as "Tiempo de margen promedio"  from assign ma          JOIN assign_submission mas on mas.assignment = ma.id      JOIN user mu on mu.id = mas.userid where course = %courseid      and ma.duedate - mas.timemodified > 0      and status = "submitted"  group by userid;', 'kind' => 'LIST'];
    private static $grupalAmountOfNP = ['sql' => 'select mu.username as "Nombre de usuario",        (select count(id) from mdl_assign where course = %courseid) - count(distinct assignment) as "Cantidad de NP" from assign_submission mas     JOIN user mu on mu.id = mas.userid where mas.assignment in (select ma2.id from mdl_assign ma2 where course = %courseid) group by userid;', 'kind' => 'LIST'];
    private static $grupalResourcesSeenByStudent = ['sql' => "select mu.username as 'Nombre de usuario', mr.name as 'Nombre del recurso', count(mlsl.id) as 'Veces visto' from logstore mlsl     join resource mr on mr.id = mlsl.objectid     join user mu on mu.id = mlsl.userid where courseid = %courseid and component = 'mod_resource' group by userid, name order by userid, name asc;", 'kind' => 'LIST'];
    private static $grupalResourcesSeenTotal = ['sql' => "select row_number() over (order by count(mlsl.id) desc) as '#', mr.name as 'Nombre del recurso', count(mlsl.id) as 'Veces visto' from logstore mlsl     join resource mr on mr.id = mlsl.objectid     join user mu on mu.id = mlsl.userid where courseid = %courseid and component = 'mod_resource' group by name order by count(mlsl.id) desc; ", 'kind' => 'LIST'];
    private static $grupalCourseTimesSeen = ['sql' => "select rank() over (order by count(mlsl.id) desc) as '#' ,mu.username as 'Nombre de usuario', count(mlsl.id) as 'Cantidad de accesos al curso' from logstore mlsl join user mu on mu.id = mlsl.userid where courseid = %courseid and mlsl.eventname = '\\\\core\\\\event\\\\course_viewed' group by mu.username order by count(mlsl.id) desc", 'kind' => 'LIST'];
    private static $grupalURLseen = ['sql' => "SELECT rank() over (order by count(mlsl.id) desc) as '#', name as 'Nombre del enlace', count(mlsl.id) as 'Cantidad de veces visto' FROM url murl     join logstore mlsl on mlsl.courseid = murl.course and component = 'mod_url' and murl.id = mlsl.objectid     join user mu on mu.id = mlsl.userid WHERE course = %courseid and action = 'viewed' group by name;", 'kind' => 'LIST'];
    private static $grupalURLSeenPerUser = ['sql' => "SELECT mu.username as 'Nombre de usuario' ,name as 'Nombre del enlace', count(mlsl.id) as 'Cantidad de veces visto' FROM url murl     join logstore mlsl on mlsl.courseid = murl.course and component = 'mod_url' and murl.id = mlsl.objectid     join mdl_user mu on mu.id = mlsl.userid WHERE course = %courseid and action = 'viewed' group by username, name;", 'kind' => 'LIST'];

    public static $gruposInteracciones = [
        'individual' => 'Indicadores Individuales',
        'grupal' => 'Indicadores de Grupo',
    ];

    /**
     * Offer a list of operations for each role.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return int
     */
    public function getCourseIndicators(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $course = $_SESSION['courseList'][$args['courseid']];
        $params = array();
        if (!is_null($course) && $course['role'] === 'student')
        {
            $params['role'] = 'student';
            $params[IndicatorHandler::$gruposInteracciones['individual']] = array_keys($this->indicators[IndicatorHandler::$gruposInteracciones['individual']]);
        } else if (!is_null($course) && $course['role'] === 'teacher'){
            $params['role'] = 'teacher';
            $params[IndicatorHandler::$gruposInteracciones['grupal']] = (array_keys($this->indicators[IndicatorHandler::$gruposInteracciones['grupal']]));
            $params[IndicatorHandler::$gruposInteracciones['individual']] = array_keys($this->indicators[IndicatorHandler::$gruposInteracciones['individual']]);
        } else {
            $params['¡Alerta! No estás relacionado con esta asignatura'] = '';
            $response = $response->withStatus(400,'No hay registros tuyos en esta asignatura');
            return $response;
        }

        $response = $response->withStatus(200);
        $response = $response->getBody()->write(json_encode($params));
        return $response;
    }

    /**
     * List of operations a student can extract.
     * @return array
     */
    public static function getIndividualIndicators()
    {
        $indicators = array();

        $indicators['Total interacciones individuales'] = self::$individualTotalInteractions;
        $indicators['Posición ránking de interacciones asignatura'] = self::$individualRankingInteractions;
        $indicators['Tiempo de margen para modificar entrega de media'] = self::$individualAVGTimeToModify;
        $indicators['Cantidad de NP'] = self::$individualAmountOfNP;
        $indicators['Glosario de recursos vistos'] = self::$individualResourcesSeen;
        $indicators['Cantidad de recursos vistos'] = self::$individualAmountResourcesSeen;
        $indicators['Cantidad de veces que has visto el curso'] = self::$individualCourseTimesSeenAmount;
        $indicators['URLs visitadas'] = self::$individualURLSeen;

        return $indicators;
    }

    /**
     * List of operations that affect a whole class.
     * @return array
     */
    public static function getGrupalIndicators()
    {
        $indicators = array();

        $indicators['Ránking interacciones de la asignatura'] = self::$grupalRankgingInteractions;
        $indicators['Tiempo de margen para modificar entrega de media por alumno'] = self::$grupalAVGTimeToModify;
        $indicators['Cantidad de NP por alumno'] = self::$grupalAmountOfNP;
        $indicators['Lista de recursos vistos por alumno'] = self::$grupalResourcesSeenByStudent;
        $indicators['Ranking de recursos más vistos'] = self::$grupalResourcesSeenTotal;
        $indicators['Cantidad de accesos al curso'] = self::$grupalCourseTimesSeen;
        $indicators['Acceso a las URL del curso'] = self::$grupalURLseen;
        $indicators['Visitas a las URL por usuario'] = self::$grupalURLSeenPerUser;

        return $indicators;
    }

    /**
     * Combination of operations
     * @return array
     */
    public static function getAllIndicators()
    {

        $indicators = array();

        $indicators[self::$gruposInteracciones['individual']] = self::getIndividualIndicators();
        $indicators[self::$gruposInteracciones['grupal']] = self::getGrupalIndicators();
        return $indicators;
    }

    private function findKey($array, $keySearch)
    {
        $ans = [];


        if (is_array($array)) {
            if (isset($array[$keySearch]['sql'])) {
                $ans = $array;
            }

            foreach ($array as $subarray) {
                $ans = array_merge($ans, $this->findKey($subarray, $keySearch));
            }
        }

        return $ans;
    }

    private function dictionaryOfCapableOperations(string $courseid)
    {
        if (isset($_SESSION['courseList'][$courseid])) {
            switch ($_SESSION['courseList'][$courseid]['role']) {
                case 'student':
                    return [self::getIndividualIndicators()];
                case 'teacher':
                    return self::getAllIndicators();
                default:
                    return [];
            }
        } else {
            return [];
        }
    }

    /**
     * Processes petitions of operations and returns them.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return int
     */
    public function extractIndicators(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $queries_array = $_POST['queries'];
        $courseid = $args['courseid'];
        $responseArray = array();



        $allIndicators = $this->dictionaryOfCapableOperations($courseid);

        foreach ($queries_array as $item) {
            $query = $this->findKey($allIndicators, $item['name'])[$item['name']];

            $sql = $query['sql'];


            $sql = str_replace('%courseid', $courseid, $sql);
            $sql = str_replace('%userid', $_SESSION['user']['id'], $sql);

            $stmt = $this->sql->prepare($sql);

            //$stmt->bindParam('%courseid', $courseid);
            //$stmt->bindParam('%courseid', $_SESSION['user']['id']);
            $stmt->execute();
            $resultset = $stmt->fetchAll();

            $responseArray[$item['name']] = ['content' => $resultset, 'kind' => $query['kind']];


        }

        return $response->getBody()->write(json_encode($responseArray));

    }
}