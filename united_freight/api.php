<?php
include 'loadDBTables.php';
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
  
if ($request[0]=='new'){
  if (createUser($mysqli) == true) {
       exit('Login success');
   } else {
     //  echo ('<br><br>login fail'. $mysqli->error);
       header("HTTP/1.1 404 Not Found");
       exit;
   }
  }
  if ($request[0]=='update'){
    if (updateUser($mysqli) == true) {
         exit('Update success');
     } else {
       //  echo ('<br><br>login fail'. $mysqli->error);
         header("HTTP/1.1 404 Not Found");
         exit;
     }
    }
    if ($request[0]=='change_password'){
      if (changePasswprd($mysqli) != null) {
           exit('Update success');
       } else {
         //  echo ('<br><br>login fail'. $mysqli->error);
           header("HTTP/1.1 404 Not Found");
           exit;
       }
      }
    
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


// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',$request[0]);
if (sizeof($request)==2){
  $key =  preg_replace('/[^a-z0-9_]+/i','',$request[1]);
}  
if (!array_key_exists($table, $tablesjson)){
  http_response_code(404);
  die('Error retrieving information');
}
// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($mysqli) {
  if ($value===null) return null;
  return mysqli_real_escape_string($mysqli,(string)$value);
 },array_values($input));
  

// build the SET part of the SQL command
$set = '';
$chars = array("CHAR","VARCHAR");
for ($i=0;$i<count($columns);$i++) {
  if ($columns[$i]!==''){
    $set.=($i>0?',':'').'`'.$columns[$i].'`=';    
   if (in_array($tablesjson->{$table}->{"fields"}->{$columns[$i]},$chars)){
      $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
   }else{
     $set.=($values[$i]===null?'NULL':floatval($values[$i]));
   } 
  }
  
}
$action='';  
if (!$key){
  $action=" ,'' as actions ";
}  

$where=''; 
$keyfields=explode(",",$tablesjson->{$table}->{"pkey"});
for ($x = 0; $x < sizeof($keyfields) && sizeof($request)>1 ; $x++) {
   if($x>0){
     $where=$where." and ";
   }
   $fieldvalue=$request[$x+1];
   if ($tablesjson->{$table}->{"fields"}->{$keyfields[$x]}=="CHAR"){
     $fieldvalue="'".$fieldvalue."'";
   }
   $where =$where." ".$keyfields[$x]."=".$fieldvalue; 
 }

// create SQL based on HTTP method
switch ($method) {
  case 'GET':    
  //$tablesjson->{"address"}->{"pkey"}
    $sql = "select * ".$action." from `$table`".($where?" WHERE $where":''); break;
  case 'PUT':
    $sql = "update `$table` set $set where $where"; break;
  case 'POST':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete from `$table` where $where"; break;
}

//API syntax table/api/verb/key
//Check for customized service
if (sizeof($request)>1){
  if ($request[1]=='api'){
    include 'service/'.$table.'Service.php';
  }
}
 
// excecute SQL statement
$result = mysqli_query($mysqli,$sql);
//echo mysqli_num_rows($result).'<br>'.$method.'  '.$sql.'<br>'.mysqli_error($mysqli);

// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

// print results, insert id or affected row count
if ($method == 'GET' || ($request[1]=='api' && $request[2]=='search')) {
   if (!$key || $request[2]=='list'){
      echo '[';
     }  
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
	   $rec= mysqli_fetch_object($result);	
      if ((!$key && sizeof($request)==1) || ($request[2]=='list' && $request[1]=='api') ){   
        $keyvalues=''; 
        for ($x=0; $x<sizeof($keyfields); $x++){
            if ($keyvalues){
              $keyvalues=$keyvalues.'/';
            }
            $tmp=trim($keyfields[$x]);
            $keyvalues=$keyvalues.$rec->$tmp;
            //var_dump($rec);
           // echo  "\n\n".$keyfields[1]." jey value ".$rec->$keyfields[$x]."  >>".$rec->code."<<  ".$x;
        }
         $rec->id=$keyvalues;
         $rec->actions='<button onclick="editThis(\''.$keyvalues.'\',this)" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
	       $rec->actions.='<button onclick="deleteThis(\''.$keyvalues.'\',this)" style="margin-left:10px" class="btn btn-danger btn-sm"  data-target="#delete-button" ><i class="glyphicon glyphicon-trash"></i></button>';
      } 
	   echo ($i>0?',':'').json_encode($rec);
  }
  if (!$key || $request[2]=='list') echo ']';
  elseif (mysqli_num_rows($result)==0) echo '{}';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($mysqli);
} else {
  echo mysqli_affected_rows($mysqli);
}
  
// close mysql connection
mysqli_close($mysqli);
