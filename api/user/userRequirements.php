<?php


namespace API\user;


use http\Env\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class userRequirements
{

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        $dbSettings = $c->get('sql');
        $this->sql = new \PDO('mysql:host='. $dbSettings['address'] . ';dbname=' . $dbSettings['dbname'], $dbSettings['userNameDB'], $dbSettings['passwordDB']);
    }

    public function curl_call (string $url)
    {
        //Get token



        $handle = curl_init();


        // Set the url
        curl_setopt($handle, CURLOPT_URL, $url);
        // Set the result output to be a string.
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($handle);

        curl_close($handle);

        return $output;

    }

    public function login (RequestInterface $request, ResponseInterface $response, array $args)
    {
        $username = $_POST['login'];
        $password = $_POST['password'];


        //Get token
        $ch = curl_init();
        $username = curl_escape($ch, $username);
        $password = curl_escape($ch, $password);
        $url_token = "https://estudy.salle.url.edu/login/token.php?username={$username}&password={$password}&service=moodle_mobile_app";
        curl_setopt($ch, CURLOPT_URL, $url_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url_token);
        $token = curl_exec($ch);
        curl_close($ch);

        $token = $this->curl_call($url_token);
        $token = json_decode($token);

        if (!$token || isset($token->error))
        {
            $response = $response->withStatus(400);
            $response = $response->getBody()->write(json_encode(array('status'=> false, 'reason'=>'No user with said password found')));
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


                $response = $response->withStatus(200);
                $response = $response->getBody()->write(json_encode(array('status'=> true)));
                return $response;
        }
    }

    public function logout(RequestInterface $request, ResponseInterface $response, array $args)
    {
        session_destroy();

        $response->getBody()->write('out');
        $response = $response->withStatus(200);

        return $response;
    }

    public function checkUserSession(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $response = $response->withStatus(200);
        $response = $response->getBody()->write(json_encode($_SESSION['user']));
        return $response;
    }
}