<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT b.*,c.description FROM `billoflading_discount` as b left join charge_item as c on b.charge_item_id=c.id WHERE  b.billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;
    case 'select':         
    $sql="SELECT b.*,c.description FROM `billoflading_discount` as b left join charge_item as c on b.charge_item_id=c.id WHERE  b.billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);;
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
