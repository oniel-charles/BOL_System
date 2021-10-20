
<?php
switch ($request[2]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,vessel_name as description,country_id FROM `vessel` order by vessel_name ";
    break;
    case 'excel':    
    $sql="SELECT v.vessel_name,v.vessel_code,v.lloyd_number,c.country_name from vessel as v left join country as c on v.country_id=c.id  order by vessel_name ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
