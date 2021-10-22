
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,shipping_line_name as description FROM shipping_line order by shipping_line_name ";
    break;
    case 'excel':    
    $sql="SELECT * from shipping_line  order by shipping_line_name ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
