<?php


namespace API\indicators;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndicatorHandler
{

    private ContainerInterface $container;
    private \PDO $sql;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host=' . $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }

    private static $individualTotalInteractions = ['sql' => 'SELECT firstname, count(action) as amount FROM mdl_logstore_standard_log mlsl JOIN mdl_user mu ON mu.id = mlsl.userid WHERE courseid = %courseid AND userid = %userid group by userid', 'kind' => "COUNTER"];
    private static $individualRankingInteractions =  ['sql' => 'select ranking.userid, ranking.times, ranking.position as amount, mu.username  from mdl_user mu join ( SELECT userid, count(action) as "times", row_number() over (order by count(action) desc) position FROM mdl_logstore_standard_log mlsl where courseid = %courseid group by userid order by count(action) desc ) ranking on mu.id = ranking.userid where userid = %userid', 'kind' => 'COUNTER'];
    private static $individualAVGTimeToModify =  ['sql' => 'select userid, sec_to_time(avg(time_to_sec(timediff(from_unixtime(ma.duedate), from_unixtime(mas.timemodified))))) as amount from mdl_assign ma JOIN mdl_assign_submission mas on mas.assignment = ma.id where course = %courseid   and ma.duedate - mas.timemodified > 0   and status = "submitted" and userid = %userid group by userid;', 'kind' => 'COUNTER'];
    private static $individualAmountOfNP =  ['sql' => 'select userid, (select count(id) from mdl_assign where course = %courseid) - count(distinct assignment) as amount from mdl_assign_submission mas where mas.assignment in (select ma2.id from mdl_assign ma2 where course = %courseid) and userid = %userid group by userid;', 'kind' => 'COUNTER'];

    private static $grupalRankgingInteractions =  ['sql' => 'select mu.username as login, ranking.times as amount, ranking.position as position  from mdl_user mu join ( SELECT userid, count(action) as "times", row_number() over (order by count(action) desc) position FROM mdl_logstore_standard_log mlsl where courseid = %courseid group by userid order by count(action) desc ) ranking on mu.id = ranking.userid','kind' => 'LIST'];
    private static $grupalAVGTimeToModify =  ['sql' => 'select userid as userid, sec_to_time(avg(time_to_sec(timediff(from_unixtime(ma.duedate), from_unixtime(mas.timemodified))))) as amount from mdl_assign ma     JOIN mdl_assign_submission mas on mas.assignment = ma.id where course = %courseid   and ma.duedate - mas.timemodified > 0   and status = "submitted" group by userid;', 'kind' => 'LIST'];
    private static $grupalAmountOfNP =  ['sql' => 'select userid as userid, (select count(id) from mdl_assign where course = %courseid) - count(distinct assignment) as amount from mdl_assign_submission mas where mas.assignment in (select ma2.id from mdl_assign ma2 where course = %courseid) group by userid;', 'kind' => 'LIST'];

    public static $gruposInteracciones = [
        'individual' => 'Individuales',
        'grupal' => 'Grupales',
    ];

    public static function getIndividualIndicators()
    {
        $indicators = array();

        $indicators['Total interacciones individuales'] = self::$individualTotalInteractions;
        $indicators['Posición ránking de interacciones asignatura'] = self::$individualRankingInteractions;
        $indicators['Tiempo de margen para modificar entrega de media'] = self::$individualAVGTimeToModify;
        $indicators['Cantidad de NP'] = self::$individualAmountOfNP;

        return $indicators;
    }

    public static function getGrupalIndicators()
    {
        $indicators = array();

        $indicators['Ránking interacciones de la asignatura'] = self::$grupalRankgingInteractions;
        $indicators['Tiempo de margen para modificar entrega de media por alumno'] = self::$grupalAVGTimeToModify;
        $indicators['Cantidad de NP por alumno'] = self::$grupalAmountOfNP;
        return $indicators;
    }

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

    public function extractIndicators(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $queries_array = $_POST['queries'];
        $courseid = $args['courseid'];
        $responseArray = array();

        $allIndicators = self::getAllIndicators();
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

            $responseArray[$item['name']] = ['content'=>$resultset, 'kind' => $query['kind']];


        }

        return $response->getBody()->write(json_encode($responseArray));

    }
}