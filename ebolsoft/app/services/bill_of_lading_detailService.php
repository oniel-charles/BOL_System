<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by description_of_goods desc ";
    break;
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by description_of_goods desc ";
    break;
    case 'summary':         
      $sql="SELECT b.booking_id as id,sum(`number_of_items`) as number_of_items,sum(weight) as weight , sum(measure) as measure FROM `bill_of_lading_detail` d left join bill_of_lading as b on d.billoflading_id=b.id WHERE b.parent_bol=0 and b.booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." group by b.booking_id  ";
      break;
    case 'manifest-detail':         
    $sql="SELECT * FROM `bill_of_lading_detail` where billoflading_id in (select id from bill_of_lading where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]).")";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
