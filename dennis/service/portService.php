
<?php
switch ($request[2]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id,port_name as description FROM `port` order by port_name ";
    break;
    case 'excel':    
    $sql="SELECT p.port_code ,p.port_name,c.country_name FROM `port` as p left join country as c on p.country_id=c.id order by p.port_name ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
