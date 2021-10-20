<?php
switch ($this->requestUrl[1]) {
  case 'code':         
    $sql="SELECT * FROM `system_values` where code='".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])."'";
    break;
    case 'select':         
    $sql="SELECT * FROM `system_values` ";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
