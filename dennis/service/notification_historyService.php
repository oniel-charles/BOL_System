
<?php
switch ($request[2]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * from notification_history  where billoflading_id=".$request[3];
    break;
    case 'bol': 
      $sql="SELECT * from notification_history  where billoflading_id=".$request[3]." order by notification_date desc";
      break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
