<?php
switch ($service_type) {
  case 'cancel':         

  date_default_timezone_set('America/Jamaica');
  $sql=" update  bill_of_lading set receipt_processed=0 where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
  $result = mysqli_query($mysqli,$sql);
   
  $sql=" update receipt set cancelled=1,cancel_date=".date('Ymd').",cancel_time=".date('hi').",cancel_by=".$claims['id']." where id=".preg_replace('/[^a-z0-9_]+/i','',$request[2]);
  $result = mysqli_query($mysqli,$sql);
    break;
    case 'DELETE':
      $sql = "delete from `$table`xx where $where"; 
      break;
  }

?>
