<?php
switch ($request[2]) {
  case 'code':         
    $sql="SELECT * FROM `system_values` where code='".preg_replace('/[^a-z0-9_]+/i','',$request[3])."'";
    break;
    case 'select':         
    $sql="SELECT * FROM `system_values` ";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
