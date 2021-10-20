<?php
include 'loadDBTables.php';
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
//var_dump($input); 
// connect to the mysql database
$link = mysqli_connect('localhost', 'root', '', 'schooldb');
mysqli_set_charset($link,'utf8');
  
// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',$request[0]);
$key =  preg_replace('/[^a-z0-9_]+/i','',$request[1]);
  
if (!array_key_exists($table, $tablesjson)){
  http_response_code(404);
  die('Error retrieving information');
}
// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($link) {
  if ($value===null) return null;
  return mysqli_real_escape_string($link,(string)$value);
 },array_values($input));
  

// build the SET part of the SQL command
$set = '';
for ($i=0;$i<count($columns);$i++) {
  $set.=($i>0?',':'').'`'.$columns[$i].'`=';
  $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
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
   if ($tablesjson->{$table}->{"fields"}->{$keyfields[$x]}=="char"){
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

//Check for customized service
if (sizeof($request)>1){
  if ($request[1]=='api'){
    include 'service/'.$table.'Service.php';
  }
}
// echo '<br>'.$method.'  '.$sql.'<br>';
// excecute SQL statement
$result = mysqli_query($link,$sql);
  
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($link));
}

// print results, insert id or affected row count
if ($method == 'GET' ) {
   if (!$key || $request[2]=='list'){
      echo '[';
     }  
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
	   $rec= mysqli_fetch_object($result);	
      if (!$key || $request[2]=='list'){   
        $keyvalues=''; 
        for ($x=0; $x<sizeof($keyfields); $x++){
            if ($keyvalues){
              $keyvalues=$keyvalues.'/';
            }
            $tmp=trim($keyfields[$x]);
            $keyvalues=$keyvalues.$rec->$tmp;
        }
         $rec->id=$keyvalues;
         $rec->actions='<button onclick="editThis(\''.$keyvalues.'\',this)" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
	       $rec->actions.='<button onclick="deleteThis(\''.$keyvalues.'\',this)" style="margin-left:10px" class="btn btn-danger btn-sm"  data-target="#delete-button" ><i class="glyphicon glyphicon-trash"></i></button>';
      } 
	   echo ($i>0?',':'').json_encode($rec);
  }
  if (!$key || $request[2]=='list') echo ']';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}
  
// close mysql connection
mysqli_close($link);
