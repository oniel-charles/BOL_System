
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id, description,item_rate,basis,gct,currency_id FROM `charge_item` order by description ";
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
