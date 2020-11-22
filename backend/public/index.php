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
    'redirect_uri' => 'https://codechefarena.herokuapp.com/',
    'website_base_url' => 'https://codechefarena.herokuapp.com/',
    'state' =>'xyz',
    'scope' => 'public',
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

// home router
$app->get('/', function(Request $req, Response $res, array $args) {
    $db = new database();
    $db->connect();
    $dummyData = array('abc','abcd','abcde','abcdef','bcd','bcde','cdef','cde','def','ghi','jkl','mno','dummy','2-D','easy','hard','medium','array','sahil','aman','stack','dp','greedy');
    $res->withStatus(200)->write(json_encode(array("status"=>"OK", "tags" => $dummyData)));
    return $res;
});


$app->run();


