<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/tags', function(Request $req, Response $res, $args) {

    /*
      Rresponse => for response of codechef api
      response => json decoded response of codechef api
      res => response of the route
      */

    $params = array(
        'grant_type' => 'client_credentials',
        'scope' => 'public',
        'client_id' => $_SESSION['client_info']['client_id'],
        'client_secret' => $_SESSION['client_info']['client_secret'],
        'redirect_uri' => $_SESSION['client_info']['redirect_uri'],
    );
    try {
        $Rresponse = $GLOBALS['client']->request('POST','/oauth/token',['form_params' => $params]);
        if($Rresponse->getStatusCode()!=200)
            {throw new Exception("failed");}

        $body = $Rresponse->getBody();
        //echo $body;
        $response = json_decode($body,true);
        $result = $response['result']['data'];
        $access_token = $result['access_token'];
        $scope = $result['scope'];

        // get public tags
        $Rresponse = $GLOBALS['client']->request('GET','/tags/problems?limit=100&offset=0',
            ['headers'=>array('Content-Type'=>'application/json','Authorization'=>'Bearer '.$access_token)]
        );
        if($Rresponse->getStatusCode()!=200)
            {throw new Exception("failed");}
        $body = $Rresponse->getBody();
        $response = json_decode($body,true)['result']['data']['content'];
        $res->getBody()->write(json_encode($response));
    } catch(Exception $e) {
        $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
    }
    return $res->withHeader('content-type', 'application/json');
});


$app->get('/private/tags', function(Request $req, Response $res, $args) {

    /*
      Rresponse => for response of codechef api
      response => json decoded response of codechef api
      res => response of the route
    */

    $username = $_GET['username'];
    $sql = "Select * from userData where username ='".$username."';";
    try {
        $db = new database();
        $db = $db->connect();
        $statement = $db->query($sql);
        $user = $statement->fetchAll(PDO::FETCH_OBJ);
        //return $res->getBody()->write(json_encode($user));
        $user = json_decode(json_encode($user[0]), true);
        // $access_token = $user['access_token'];
        // if($_GET['access_token'] != $access_token) {
        //     return $res->getBody()->write(json_encode(array("status" => "FAILED","data" => ["message" => "Authorization Failed!!",'access_token' => $access_token])));
        // } 
        $sql = "select tags from user_defined_tags where username ='".$username."';";
        $statement = $db->query($sql);
            $tags = $statement->fetchAll(PDO::FETCH_OBJ);
            $tags = json_decode(json_encode($tags), true);
            $res->getBody()->write(json_encode(array("status" => "OK", "data" => $tags))); 
    } catch(Exception $e) {
        $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
    }
    return $res;//->withHeader('content-type', 'application/json');
});

$app->post('/private/tags/add', function(Request $req, Response $res, $args) {
    $body = $req->getParsedBody();
    $sql = "insert into user_defined_tags(username,tags,problem_code,cc_tags,attempted,solved,author) values('".$body['username']."','".$body['tags']."','".$body['problem_code']."','".$body['cc_tags']."','".$body['attempted']."','".$body['solved']."','".$body['author']."')";
    try {
        $db = new database();
        $db = $db->connect();
        if($db->query($sql) == true) {
            return $res->getBody()->write(json_encode(array("status" => "OK","data" => ["username" => $body['username'] , "tags" => $body['tags']])));
        }
        return $res->getBody()->write(json_encode(array("status" => "ERROR", "data" => ["message" => "DB Error"])));
    } catch(Exception $e) {
         return $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
    }
    
});