
<?php
switch ($request[2]) {
    case 'select':    
    $sql="SELECT d.receipt_id as receipt_number,d.amount,c.description,amount as paid FROM `receipt_detail` as d left join charge_item as c on charge_item_id=c.id WHERE d.receipt_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
    break;
    case 'excel':    
    $sql="SELECT d.amount,i.description,v.arrival_date,s.vessel_name,u.user_name as cashier,r.id,r.payee,r.receipt_date,r.receipt_total,p.port_name,b.bill_of_lading_number FROM (((((((receipt_detail as d left join receipt as r on d.receipt_id=r.id) left join charge_item as i on d.charge_item_id=i.id) left join bill_of_lading as b on r.billoflading_id=b.id) left join port as p on b.port_of_loading=p.id) left join user_profile as u on r.created_by=u.id) left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) where (r.cancelled is null or r.cancelled=0) and  r.receipt_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
