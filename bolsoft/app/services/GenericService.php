<?php

$mysqli=$this->db->conn;
// retrieve the key from the path
$key=null;
if (sizeof($this->requestUrl)==2){
  $key =  preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[1]);
} 

if($this->requestMethod=='PUT' || $this->requestMethod=='POST' ) {
        // escape the columns and values from the input object
        $columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($this->post_data));
        $values = array_map(function ($value) use ($mysqli) {
        if ($value===null) return null;
        return mysqli_real_escape_string($mysqli,(string)$value);
        },array_values($this->post_data));
        
        // build the SET part of the SQL command
        $set = '';
        for ($i=0;$i<count($columns);$i++) {
        $set.=($i>0?',':'').'`'.$columns[$i].'`=';
        $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
        }
}

$action='';  
if (!$key){
  $action=" ,'' as actions ";
}  

$where=getWhereClause($this->db->models->{$this->table}, $this->requestUrl); 
switch ($this->requestMethod) {
    case 'GET':    
      $sql = "select * ".$action." from `$this->table`".($where?" WHERE $where":''); break;
    case 'PUT':
      $sql = "update `$this->table` set $set where $where"; break;
    case 'POST':
      $sql = "insert into `$this->table` set $set"; break;
    case 'DELETE':
      $sql = "delete from `$this->table` where $where"; break;
  }


/*
$result = mysqli_query($mysqli,$sql);
$rec= mysqli_fetch_object($result);	
$rec->actions='<button onclick="editThis(\' OCCC \',this)" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
var_dump($rec);
  */     


 function getWhereClause($model,$url){
    $where='';
    $keyfields=explode(",",$model->{"pkey"});
    for ($x = 0; $x < sizeof($keyfields) && sizeof($url)>1 ; $x++) {
       if($x>0){
         $where=$where." and ";
       }
       $fieldvalue=$url[$x+1];
       if ($model->{"fields"}->{$keyfields[$x]}=="char"){
         $fieldvalue="'".$fieldvalue."'";
       }
       $where =$where." ".$keyfields[$x]."=".$fieldvalue; 
     }
     return $where;
  }

?>