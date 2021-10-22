
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * FROM `product_rate` where product_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by effective_date desc ";

    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
