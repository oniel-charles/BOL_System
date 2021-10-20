<?php
switch ($request[2]) {
  case 'code':         
    $sql="SELECT * FROM `translation_source` where code='".$request[3]."' ";
    
    break;
    case 'select':         
    $sql="SELECT * FROM `translation_source` order by description";;
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
