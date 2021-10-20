<?php
switch ($request[2]) {
  case 'list':         
    $sql="SELECT b.*,currency_code FROM `bill_of_lading_other_charge` as b left join currency on b.currency_id=currency.id WHERE b.billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
    break;
    case 'select':         
    $sql="SELECT b.*,currency_code FROM `bill_of_lading_other_charge` as b left join currency on b.currency_id=currency.id WHERE b.billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);;
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
