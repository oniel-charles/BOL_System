<?php
switch ($request[2]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by description_of_goods desc ";
    break;
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by description_of_goods desc ";
    break;
    case 'manifest-detail':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id in (select id from bill_of_lading where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]).")";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
