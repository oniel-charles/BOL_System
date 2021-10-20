
<?php
switch ($request[2]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,country_name as description FROM `country` order by country_name ";
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
