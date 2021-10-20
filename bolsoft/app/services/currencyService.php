
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,currency_name as description FROM `currency` order by currency_name ";
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
