
<?php
switch ($method) {
    case 'GET':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT description,`score_scale`.*,'' as actions FROM `score_scale` left join `score_type` on `score_scale`.`score_type_id`=`score_type`.`id` where score_type_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;
    case 'PUT':
      $sql = "update `$table` set $set where $where"; 
      break;
    case 'POST':
      $sql = "insert into `$table` set $set"; 
      break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
