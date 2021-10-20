
<?php
switch ($request[2]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT *,id as action from voyage_container where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by container_number";
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
