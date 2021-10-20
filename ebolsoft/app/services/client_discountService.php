<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT * from clien_discount  WHERE client_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;
    case 'select':         
    $sql="SELECT d.*,c.description FROM `client_discount` as d left join charge_item as c on d.id=c.id where d.client_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);;
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
