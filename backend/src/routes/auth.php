<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//login route
$app->get('/login', function (Request $req, Response $res, $args) {

      /*
      Rresponse => for response of codechef api
      response => json decoded response of codechef api
      res => response of the route
      */

      if(isset($_GET['code'])){
              $code = $_GET['code'];
              $params = array('grant_type'=>'authorization_code',
                        'code'=>$code,
                        'client_id'=>$_SESSION['client_info']['client_id'],
                        'client_secret'=>$_SESSION['client_info']['client_secret'],
                       'redirect_uri'=>$_SESSION['client_info']['redirect_uri']
                       );
               try{
                     //getaccesstoken
                     $Rresponse = $GLOBALS['client']->request('POST','/oauth/token',['form_params'=>$params]);
                     if($Rresponse->getStatusCode()!=200)
                         {throw new Exception("failed");}

                     $body = $Rresponse->getBody();
                     $response = json_decode($body,true);
                     $result = $response['result']['data'];
                     $access_token = $result['access_token'];
                     $refresh_token = $result['refresh_token'];
                     $scope = $result['scope'];

                     //getuser
                     $Rresponse = $GLOBALS['client']->request('GET','/users/me',['headers'=>array('Content-Type'=>'application/json','Authorization'=>'Bearer '.$access_token)]);
                     if($Rresponse->getStatusCode()!=200)
                         {throw new Exception("failed");}
                     $body = $Rresponse->getBody();
                     $response = json_decode($body,true)["result"]["data"];

                     //addtodatabase
                     $db = new database();
                     $db = $db->connect();
                     $sql_query = '"'.$response['content']['username'].'","'.$access_token.'","'.$refresh_token.'","'.substr($response['content']['band'],0,1).'"';
                     $sql_query = "INSERT into userData(username,access_token,refresh_token,band) values (".$sql_query.");";
                     if($db->query($sql_query)==TRUE){

                       $res->getBody()->write(json_encode(
                         array("status"=>"OK","data"=>["username"=>$response['content']['username'],"access_token"=>$access_token,"refresh_token" => $refresh_token,"band"=>substr($response['content']['band'],0,1)])
                       ));
                     }
                     else{
                       $res->getBody()->write(json_encode(
                         array("status"=>"error","data"=>["message"=>"database error"])
                       ));
                     }

               }catch(Exception $e){
                    $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
                 }

      } else {
          $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>"API error"])));
      }
      return $res->withHeader('Content-Type', 'application/json');
});

//refresh route
$app->get('/refresh', function (Request $req, Response $res, $args) {

      /*
      Rresponse => for response of codechef api
      response => json decoded response of codechef api
      res => response of the route
      */

        $username = $_GET['username'];
        $sql = "Select * from userData where username ='".$username."';";
    try{
      //get refresh token from db
      $db = new database();
      $db = $db->connect();
      $statement = $db->query($sql);
      $user = $statement->fetchAll(PDO::FETCH_OBJ);
      $user = json_decode(json_encode($user[0]), true);
      $refresh_token = $user['refresh_token'];
      //$user = json_decode($user);

      //get new accesstoken
      $params = array(
                'grant_type'=>'refresh_token',
                'refresh_token'=>$refresh_token,
                'client_id'=>$_SESSION['client_info']['client_id'],
                'client_secret'=>$_SESSION['client_info']['client_secret']
               );

      $Rresponse = $GLOBALS['client']->request('POST','/oauth/token',['form_params'=>$params]);
      if($Rresponse->getStatusCode()!=200)
          {throw new Exception("failed");}

      $body = $Rresponse->getBody();
      $response = json_decode($body,true);
      $result = $response['result']['data'];
      $access_token = $result['access_token'];
      $refresh_token = $result['refresh_token'];
      $sql_str = "Update userData set access_token = '".$access_token."',refresh_token = '".$refresh_token."',login_time = NOW(),active = 'T' where username = '".$username."';";
      $db->query($sql_str);

      $res->getBody()->write(json_encode(
        array("status"=>"OK","data"=>["message"=>"successfully refreshed"])
      ));
    }catch(Exception $e){
      $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>$e->getMessage()])));
    }

    return $res->withHeader('Content-Type', 'application/json');
});


//logout route
$app->get('/logout', function(Request $req,Response $res,$args){

    /*
      Rresponse => for response of codechef api
      response => json decoded response of codechef api
      res => response of the route
      */

    $sql = "DELETE from userData where username = '".$_GET['username']."';";
    try{
        $db = new database();
        $db = $db->connect();
        if($db->query($sql)==TRUE){

              $res->getBody()->write(json_encode(
                array("status"=>"OK","data"=>["message"=>"logged out successfully"])
              ));
        }
        else{
          $res->getBody()->write(json_encode(
            array("status"=>"error","data"=>["message"=>"database error"])
          ));
        }
    }catch(Exception $e){
      $res->getBody()->write(json_encode(array("status"=>"error","data"=>["message"=>"server error"])));
    }
    return $res->withHeader('Content-Type', 'application/json');
});