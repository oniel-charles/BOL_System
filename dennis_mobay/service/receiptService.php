
<?php
switch ($request[2]) {
    case 'date_range':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * from receipt where receipt_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
    break;
    case 'excel':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT v.arrival_date,s.vessel_name,r.cancel_date,r.cancel_time,x.user_name as cancel_by,u.user_name as cashier,r.cancelled,r.id,r.payee,r.receipt_date,r.receipt_total,p.port_name,b.bill_of_lading_number FROM ((((((receipt as r left join bill_of_lading as b on r.billoflading_id=b.id) left join port as p on b.port_of_loading=p.id) left join user_profile as u on r.created_by=u.id) left join user_profile as x on r.cancel_by=x.id) left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) where r.receipt_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
    break;
    
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
