<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading_container` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by container_number desc ";
    break;
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading_container` where billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by container_number desc ";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
