
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT b.*,c.size_type_code,c.description,c.maximum_cude FROM `booking_container` as b left join container_size_type c on b.container_size_type_id=c.id  where b.booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);

    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
