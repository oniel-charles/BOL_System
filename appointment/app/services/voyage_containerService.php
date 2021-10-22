
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT *,id as action from voyage_container where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by container_number";
    $this->addActionButtons='Yes';
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
