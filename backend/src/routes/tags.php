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


// $app->get('/private/tags', function(Request $res, Response $res, $args) {

//     /*
//       Rresponse => for response of codechef api
//       response => json decoded response of codechef api
//       res => response of the route
//     */

//     $username = $_GET['username'];
//     $sql = "Select * from userData where username ='".$username."';";

// })