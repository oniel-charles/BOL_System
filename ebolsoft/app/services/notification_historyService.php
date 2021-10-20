
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * from notification_history  where billoflading_id=".$this->requestUrl[2];
    break;
    case 'bol': 
      $sql="SELECT * from notification_history  where billoflading_id=".$this->requestUrl[2]." order by notification_date desc";
      break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
