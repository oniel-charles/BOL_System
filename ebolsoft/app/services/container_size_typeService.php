
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,size_type_code  FROM `container_size_type` order by size_type_code ";
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
