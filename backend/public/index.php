<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

// including database class
require __DIR__ . '/../src/config/database.php';

// start session
session_start();
// add client details in global session.
$_SESSION['client_info'] = array(
    "client_id" => "1d6ff67cd4f31121f089d15c1cbb93b8",
    "client_secret" => "ea8252be6be19573c2a9caf4bcac2d7b",
    'api_endpoint' => 'https://api.codechef.com',
    'authorization_code_endpoint' => 'https://api.codechef.com/oauth/authorize',
    'access_token_endpoint' => 'https://api.codechef.com/oauth/token',
    'redirect_uri' => 'https://lit-dawn-63895.herokuapp.com/',
    'website_base_url' => 'https://lit-dawn-63895.herokuapp.com/',
    'state' =>'xyz',
);

// setting configs
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

// instantiate Slim app
$app = new \Slim\app(['settings' => $config]);
// including container
$container = $app->getContainer();

// container for logger
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

// cors files
require __DIR__ . '/../src/config/cors.php';

// setting guzzle for http request
$GLOBALS['client'] = new \GuzzleHttp\Client([
    'base_uri' => $_SESSION['client_info']['api_endpoint'],
]);

// home router
$app->get('/', function(Request $req, Response $res, array $args) {

    /*
      Rresponse => for response of codechef api
      response => json decoded response of codechef api
      res => response of the route
      */

    $username = $_GET['username'];
    if($username != ''){
        $sql_query = "select * from userData where username = '".$username."' ;";
        try {
            $db = new database();
            $db = $db->connect();
            $query_result = $db->query($sql_query);
            $user = $query_result->fetchAll(PDO::FETCH_OBJ);
            $user = json_decode(json_encode($user[0]),true);
            $access_token = $user['access_token'];
            if($user['active'] == 'F') {
                $refresh_token = $user['refresh_token'];

                $params = array(
                    'grant_type'=>'refresh_token',
                    'refresh_token'=>$refresh_token,
                    'client_id'=>$_SESSION['client_info']['client_id'],
                    'client_secret'=>$_SESSION['client_info']['client_secret']
                );

                $Rresponse = $GLOBALS['client']->request('POST','oauth/token', ['form_params' => $params]);
                if($Rresponse->getStatusCode() != 200) {
                    throw new Exception('failed');
                }
                $body = $Rresponse->getBody();
                $res = json_decode($body,true);
                $result = $res['result']['data'];
                $access_token = $result['access_token'];
                $refresh_token = $result['refresh_token'];
                $sql_str = "Update userData set access_token = '".$access_token."',refresh_token = '".$refresh_token."',login_time = NOW(),active = 'T' where username = '".$username."';";
                $db->query($sql_str);
            }
            $response = array("status"=>"OK","data"=>["username" => $user["username"],"access_token" => $access_token,"band" => $user["band"]]);
            $res->getBody()->write(json_encode($response));
        } catch(Exception $e) {
            $res->getBody()->write(json_encode(
                array("status"=>"error","data"=>["message"=>$e->getMessage()])
              ));
        }
    } else {
        $response = array("status"=>"OK","data"=>["username" => '']);
        $res->getBody()->write(json_encode($response));
    }
    return $res->withHeader('content-type' , 'application/json');
});

require __DIR__.'/../src/routes/auth.php';
require __DIR__.'/../src/routes/tags.php';
require __DIR__.'/../src/routes/problems.php';

$app->run();


