
<?php
switch ($request[2]) {
  case 'date_range':    
     $sql="SELECT o.id,o.order_date,o.billoflading_id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address, s.vessel_name,v.voyage_number,v.arrival_date,o.cancel_date,u.user_name as cancel_by,o.cancelled FROM (((((shipment_order as o left join bill_of_lading as b on o.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join user_profile as u on o.cancel_by=u.id) WHERE  order_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
     break;
  case 'valid':    
     $sql="SELECT * from shipment_order where (cancelled is null or cancelled=0) and billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
     break;
  case 'excel':    
    $sql="SELECT b.consignee_name,b.consignee_address,v.arrival_date,s.vessel_name,o.cancel_date,o.cancel_time,x.user_name as cancel_by,u.user_name as cashier,o.cancelled,o.id,o.order_date,p.port_name,b.bill_of_lading_number FROM ((((((shipment_order as o left join bill_of_lading as b on o.billoflading_id=b.id) left join port as p on b.port_of_loading=p.id) left join user_profile as u on o.created_by=u.id) left join user_profile as x on o.cancel_by=x.id) left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) where   o.order_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
    break;
  case 'DELETE':
      $sql = " delete some thing"; 
      break;
  }

?>
