<?php
switch ($request[2]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading` where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by consignee_name desc ";
    break;
    case 'master-list':         
    $sql="SELECT * FROM `bill_of_lading` where master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by consignee_name desc ";
    break; 
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading` where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by consignee_name desc ";
    case 'open':         
      $status="";
      if($request[4]!="all"){ $status=" and status='".$request[4]."'";}
      
      $sql="SELECT id,bill_of_lading_number,consignee_name FROM `bill_of_lading` WHERE parent_bol<>1 and ( receipt_processed is null or receipt_processed =0)";
    break; 
    case 'house':         
    $sql="SELECT * FROM `bill_of_lading` where parent_bol=0 and voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by consignee_name desc ";
    break; 
    case 'master-select':         
    $sql="SELECT id,bill_of_lading_number,`port_of_origin`,`port_of_loading`,`port_of_discharge`,`port_of_delivery`,`currency_id`  FROM `bill_of_lading` where parent_bol=1 and  voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by bill_of_lading_number ";
    break; 
    case 'master-con':         
    $sql="SELECT bill_of_lading.*,bill_of_lading_container.container_number,container_size_type.size_type_code FROM ((`bill_of_lading` left join bill_of_lading_container on bill_of_lading.id=bill_of_lading_container.billoflading_id) left join container_size_type on bill_of_lading_container.container_size_type_id=container_size_type.id)  where bill_of_lading.id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
    break;   
    case 'bill-chg':         
    $sql="SELECT * FROM `bill_of_lading` as bl left join (select a.billoflading_id, a.amount,a.prepaid_flag from bill_of_lading_other_charge as a left join charge_item as b on a.charge_item_id=b.id  where b.item_code='FC') as t on bl.id=t.billoflading_id  where bl.voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and bl.master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
    break;
    case 'processing':         
    $sql="SELECT b.customer_type,b.receipt_processed,b.order_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE parent_bol<>1 and ( receipt_processed is null or receipt_processed =0)";
    break;
    case 'processing_history':         
    $sql="SELECT b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4])." and parent_bol<>1 and receipt_processed >0 ";
    break;
    case 'admin_processed':         
    $sql="SELECT b.receipt_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4])." and parent_bol<>1 and receipt_processed =2 ";
    break;
    case 'admin_open':         
    $sql="SELECT b.receipt_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$request[3])." and ".preg_replace('/[^a-z0-9_]+/i','',$request[4])." and parent_bol<>1 and (receipt_processed is null or receipt_processed =0)  ";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
