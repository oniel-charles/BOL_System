<?php
switch ($this->requestUrl[1]) {
  case 'code':         
    $sql="SELECT * FROM `translation_source` where code='".$this->requestUrl[2]."' ";
    
    break;
    case 'select':         
    $sql="SELECT * FROM `translation_source` order by description";;
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
