<?php


namespace API\user;


use API\courses\CourseHandler;
use http\Env\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserHandler
 * This class contains all information related to a Moodle User-
 * In order to log in, it calls moodle_mobile_app service to verify if the account is valid. If it's valid, basic information is stored in a server session.
 * If a user logs out, the API destroys the session, in case it was defined.
 *
 * This class is the starting point to use the API if there is an attempt to log in.
 * @package API\user
 */
class UserHandler
{
    private ContainerInterface $container;
    private \PDO $sql;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host='. $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }


    private function curl_call (string $url)
    {
        $handle = curl_init();
        // Set the url
        curl_setopt($handle, CURLOPT_URL, $url);
        // Set the result output to be a string.
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($handle);

        curl_close($handle);

        return $output;

    }

    /**
     * Store data related to a user from Moodle
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function login(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $username = $_POST['login'];
        $password = $_POST['password'];


        //Get token
        $ch = curl_init();
        $username = curl_escape($ch, $username);
        $password = curl_escape($ch, $password);
        $url_token = "https://estudy.salle.url.edu/login/token.php?username={$username}&password={$password}&service=moodle_mobile_app";
        $token = $this->curl_call($url_token);
        $token = json_decode($token);

        if (!$token || isset($token->error))
        {
            $response = $response->withStatus(400);
            $response->getBody()->write(json_encode(array('status'=> false, 'reason'=>'No user with said password found')));
            return $response;
        }
        else {
            $token = $token->token;

            $id_url = 'https://estudy.salle.url.edu/webservice/rest/server.php?moodlewsrestformat=json&wstoken=$token&wsfunction=core_webservice_get_site_info';
            $id_url = str_replace('$token', $token, $id_url);

            $answer = $this->curl_call($id_url);
            $row = json_decode($answer);

                session_start();

                $user = array(
                    'id' => $row->userid,
                    'login' => $row->username,
                    'firstname' => $row->firstname,
                );

                $_SESSION['user'] = $user;
                CourseHandler::addCoursesToSession($this->container);


                $response = $response->withStatus(200);
                $response = $response->getBody()->write(json_encode(array('status'=> true)));
                return $response;
        }
    }

    /**
     * Destroys the information related to the user on server.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function logout(RequestInterface $request, ResponseInterface $response, array $args)
    {
        if(session_status() == PHP_SESSION_ACTIVE){
            session_destroy();
        }

        $response->getBody()->write('out');
        $response = $response->withStatus(200);

        return $response;
    }

    /**
     * Returns information from the session stored.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return int
     */
    public function checkUserSession(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $response = $response->withStatus(200);
        $response = $response->getBody()->write(json_encode($_SESSION['user']));
        return $response;
    }
}