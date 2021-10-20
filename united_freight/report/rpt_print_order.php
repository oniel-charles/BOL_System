<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';
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

$claims=authenticateToken();
if ($claims==null){
         header("HTTP/1.1 401 Unauthorized");
          exit('invalid token');
  }

   
  $sql="SELECT * FROM `system_values` WHERE `code`='branchcode'";
  $sysval = mysqli_query($mysqli,$sql);
  $rec= mysqli_fetch_object($sysval);	

  //exit(mysqli_error($mysqli).'./rpt_print_order_'.$rec->data_value.'.php'.mysqli_num_rows($sysval));
  if ($rec->data_value=='mby'){
    require_once './rpt_print_order_mby.php';
  }else{
    require_once './rpt_print_order_kgn.php';  
  }  
  exit();
  ?>