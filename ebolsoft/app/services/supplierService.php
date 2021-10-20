
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,supplier_name as description,address,phone,email,balance  FROM `supplier`  order by supplier_name ";

    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
