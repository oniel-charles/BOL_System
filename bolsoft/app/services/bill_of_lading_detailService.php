<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by description_of_goods desc ";
    break;
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by description_of_goods desc ";
    break;
    case 'manifest-detail':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id in (select id from bill_of_lading where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]).")";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
