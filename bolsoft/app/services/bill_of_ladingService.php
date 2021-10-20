<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading` where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by id ";
    break;
    case 'master-list':         
    $sql="SELECT * FROM `bill_of_lading` where master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by consignee_name desc ";
    break; 
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading` where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by consignee_name desc ";
    break; 
    case 'house':         
    $sql="SELECT * FROM `bill_of_lading` where parent_bol=0 and voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by consignee_name desc ";
    break; 
    case 'master-select':         
    $sql="SELECT id,bill_of_lading_number,`port_of_origin`,`port_of_loading`,`port_of_discharge`,`port_of_delivery`,`currency_id`  FROM `bill_of_lading` where parent_bol=1 and  voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by bill_of_lading_number ";
    break; 
    case 'master-con':         
    $sql="SELECT bill_of_lading.*,bill_of_lading_container.container_number,container_size_type.size_type_code FROM ((`bill_of_lading` left join bill_of_lading_container on bill_of_lading.id=bill_of_lading_container.billoflading_id) left join container_size_type on bill_of_lading_container.container_size_type_id=container_size_type.id)  where bill_of_lading.id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;   
    case 'bill-chg':         
    $sql="SELECT * FROM `bill_of_lading` as bl left join (select a.billoflading_id, a.amount,a.prepaid_flag from bill_of_lading_other_charge as a left join charge_item as b on a.charge_item_id=b.id  where b.item_code='FC') as t on bl.id=t.billoflading_id  where bl.voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and bl.master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    case 'doortodoor': 
      $sql="SELECT data_value from system_values where code='freight_id'";
      $result = mysqli_query($mysqli,$sql);
      $freight= mysqli_fetch_object($result);	 
      $range=" ";
      $asat="";
      if(isset($this->requestUrl[3]) && $this->requestUrl[2]>0){   
         $range=" and v.arrival_date  between ".$this->requestUrl[2]." and ".$this->requestUrl[3];
         $asat="  or r.receipt_date>=".$this->requestUrl[3];
      }else{
        if(isset($this->requestUrl[3]) && $this->requestUrl[2]==0){
          $range=" and v.arrival_date  <= ".$this->requestUrl[3];
          $asat="  or r.receipt_date>=".$this->requestUrl[3];
        }
      }
      $sql="SELECT sum(f.amount) as freight,v.arrival_date,pt.port_code,b.currency_id,sum(c.amount) as amt,b.id, b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number FROM ((((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join port as pt on b.port_of_origin=pt.id) left join bill_of_lading_other_charge c on b.id=c.billoflading_id) left join (select amount,billoflading_id from bill_of_lading_other_charge  where charge_item_id=$freight->data_value) as f   on b.id=f.billoflading_id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join receipt as r on b.id=r.billoflading_id) WHERE parent_bol<>1  $range and b.customer_type='doortodoor' and (c.charge_item_id is null or c.charge_item_id<>$freight->data_value)   
group by v.arrival_date,pt.port_code,b.currency_id,b.id, b.bill_of_lading_number,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number, b.customer_type 
order by pt.port_code,v.arrival_date,p.bill_of_lading_number,b.bill_of_lading_number";
    break;
    case 'outstanding':        
      $sql="SELECT data_value from system_values where code='freight_id'";
      $result = mysqli_query($mysqli,$sql);
      $freight= mysqli_fetch_object($result);	 
      $cur_date= date('Ymd') ;
      $range="  and v.arrival_date <= $cur_date ";
      $asat="";
      if(isset($this->requestUrl[3]) && $this->requestUrl[2]>0){   
         $range=" and v.arrival_date  between ".$this->requestUrl[2]." and ".$this->requestUrl[3];
         $asat="  or r.receipt_date>=".$this->requestUrl[3];
      }else{
        if(isset($this->requestUrl[3]) && $this->requestUrl[2]==0){
          $range=" and v.arrival_date  <= ".$this->requestUrl[3];
          $asat="  or r.receipt_date>=".$this->requestUrl[3];
        }
      }
      $sql="SELECT v.arrival_date,pt.port_code,i.currency_id,sum(c.amount) as amt,b.id, b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number FROM ((((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join port as pt on b.port_of_origin=pt.id) left join bill_of_lading_other_charge c on b.id=c.billoflading_id) left join charge_item as i on c.charge_item_id=i.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join receipt as r on b.id=r.billoflading_id) WHERE parent_bol<>1 $range and  (r.cancelled is null or r.cancelled=0) and ( receipt_processed is null or receipt_processed =0 $asat) and (c.charge_item_id is null or c.charge_item_id<>$freight->data_value)   
group by v.arrival_date,pt.port_code,i.currency_id,b.id, b.bill_of_lading_number,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number 
order by pt.port_code,v.arrival_date,p.bill_of_lading_number,b.bill_of_lading_number"; 
$sql="SELECT sum(f.amount) as freight,v.arrival_date,pt.port_code,b.currency_id,sum(c.amount) as amt,b.id, b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number FROM ((((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join port as pt on b.port_of_origin=pt.id) left join bill_of_lading_other_charge c on b.id=c.billoflading_id) left join (select amount,billoflading_id from bill_of_lading_other_charge  where charge_item_id=$freight->data_value) as f   on b.id=f.billoflading_id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join receipt as r on b.id=r.billoflading_id) WHERE parent_bol<>1 and  (r.cancelled is null or r.cancelled=0 ) $range and ( receipt_processed is null or receipt_processed =0 $asat) and (c.charge_item_id is null or c.charge_item_id<>$freight->data_value)   
group by v.arrival_date,pt.port_code,b.currency_id,b.id, b.bill_of_lading_number,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number 
order by pt.port_code,v.arrival_date,p.bill_of_lading_number,b.bill_of_lading_number";
//exit($sql);
      /*
      $date_range='';
      if(isset($this->requestUrl[2])){
         $date_range=' and v.arrival_date between '.$this->requestUrl[2].' and '.$this->requestUrl[3];
      }
    $sql="SELECT b.customer_type,b.receipt_processed,b.order_processed,b.notify_name,b.notify_address,'' as container_num,b.consignee_phone_num,b.shipper_name,b.shipper_phone_num,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.manifest_number,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE parent_bol<>1 and ( receipt_processed is null or receipt_processed =0)".$date_range;
    */

    break;
    case 'processing':   
      $date_range='';
      if(isset($this->requestUrl[2])){
         $date_range=' and v.arrival_date between '.$this->requestUrl[2].' and '.$this->requestUrl[3];
      }
    $sql="SELECT b.customer_type,b.receipt_processed,b.order_processed,b.notify_name,b.notify_address,'' as container_num,b.consignee_phone_num,b.shipper_name,b.shipper_phone_num,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.manifest_number,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE parent_bol<>1 and ( receipt_processed is null or receipt_processed =0)".$date_range;
    

    break;
    case 'processing_container':         
      $sql="SELECT b.id,bc.container_number  FROM ((`bill_of_lading` as b  left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join bill_of_lading_container as bc on p.id=bc.billoflading_id)  WHERE b.parent_bol<>1 and ( b.receipt_processed is null or receipt_processed =0)";
      break;
    case 'processing_history':         
    $sql="SELECT v.manifest_number,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3])." and parent_bol<>1 and receipt_processed >0 ";
    break;
    case 'admin_processed':         
    $sql="SELECT b.receipt_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3])." and parent_bol<>1 and receipt_processed =2 ";
    break;
    case 'admin_open':         
    $sql="SELECT b.receipt_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3])." and parent_bol<>1 and (receipt_processed is null or receipt_processed =0)  ";
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
