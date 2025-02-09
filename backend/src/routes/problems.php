<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/tags/problems', function(Request $req, Response $res, $args) {

    $filters = $_GET['filters'];

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
        $Rresponse = $GLOBALS['client']->request('GET','/tags/problems?filter='.$filters,
            ['headers'=>array('Content-Type'=>'application/json','Authorization'=>'Bearer '.$access_token)]
        );
        if($Rresponse->getStatusCode()!=200)
            {throw new Exception("failed");}
        $body = $Rresponse->getBody();
        $body = json_decode($body,true);
        if($body['status'] == "OK") {
            $response = $body['result']['data']['content'];
            $res->getBody()->write(json_encode($response));
        } else {
            $res->getBody()->write(json_encode(array("status"=>"NOT FOUND")));
        }
        //$response = json_decode($body,true)['result']['data']['content'];
        //$res->getBody()->write(json_encode($response));
    } catch(Exception $e) {
        $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
    }
        return $res->withHeader('content-type', 'application/json');
});

$app->get('/private/problems',function(Request $req, Response $res, $args) {
    $username = $_GET['username'];
    $sql = "Select * from userData where username ='".$username."';";
    try {
        $db = new database();
        $db = $db->connect();
        $statement = $db->query($sql);
        $user = $statement->fetchAll(PDO::FETCH_OBJ);
        //return $res->getBody()->write(json_encode($user));
        $user = json_decode(json_encode($user[0]), true);
        $sql = "select * from user_defined_tags where tags ='".$_GET['filter']."';";
        $statement = $db->query($sql);
        $tags = $statement->fetchAll(PDO::FETCH_OBJ);
        $tags = json_decode(json_encode($tags), true);
        $res->getBody()->write(json_encode(array("status" => "OK", "data" => $tags))); 
    } catch(Exception $e) {
        $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
    }
    return $res;//->withHeader('content-type', 'application/json');
});