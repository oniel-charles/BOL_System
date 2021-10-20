<?php
require_once 'user_access.php';
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
//var_dump($input); 
// connect to the mysql database
if (mysqli_connect_errno()){
  header("HTTP/1.1 401 Unauthorized");
  exit(mysqli_connect_error());
}
mysqli_set_charset($mysqli,'utf8');
  
if ($request[0]=='login'){
     if (login($mysqli) == true) {
          exit('Login success');
      } else {
        //  echo ('<br><br>login fail'. $mysqli->error);
          header("HTTP/1.1 401 Unauthorized");
          exit;
      }
}else{
  $claims=authenticateToken();
  if ($claims==null){
         header("HTTP/1.1 401 Unauthorized");
          exit('invalid token');  
  }
}

//Redirect Service example /service.php/billoflading/charges/:blid
$service_type=$request[1];
$bl_id=preg_replace('/[^a-z0-9_]+/i','',$request[2]);
 include 'service/'.$request[0].'_Service.php';
 
// close mysql connection
mysqli_close($mysqli);
