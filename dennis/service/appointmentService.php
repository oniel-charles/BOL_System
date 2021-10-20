
<?php
switch ($request[2]) {
    case 'select':    
    $sql="SELECT id, appointment_date  FROM `appointment` order by appointment_date ";
    break;
    case 'consignee':    
      $sql="SELECT * from appointment where billoflading_id= ".$request[3];
      break;
    case 'day_count':    
        $sql="SELECT count(*) as appointment_count from appointment where appointment_date=".$request[3];
        break;  
    case 'list':    
      $status="";
      if($request[5]!="all"){ $status=" and status='".$request[5]."'";}
        $sql="SELECT a.id,a.billoflading_id,a.trn,a.uba_code,a.status,a.appointment_date,b.receipt_processed,b.bill_of_lading_number,b.consignee_name FROM appointment as a left join bill_of_lading as b on a.billoflading_id=b.id where a.appointment_date between ".$request[3]." and ".$request[4].$status;
        break;  
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
