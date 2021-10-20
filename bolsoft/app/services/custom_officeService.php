
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT code as id, description FROM `custom_office` order by description ";
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
