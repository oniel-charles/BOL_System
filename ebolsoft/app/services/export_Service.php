<?php
switch ($this->requestUrl[1]) {
  case 'booking':         
    $sql="SELECT b.id,b.vessel_id,c.container_number,b.contract_number,b.manifest_number,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM ((booking as b left join port as p on b.port_of_loading=p.id) left join booking_container as c on b.id=c.booking_id)  where b.status='Approved'  order by sail_date desc limit 20";
    $sql="SELECT b.id,b.vessel_id,s.size_type_code,c.container_number,b.contract_number,b.manifest_number,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM (((booking as b left join port as p on b.port_of_loading=p.id) left join booking_container as c on b.id=c.booking_id) left join container_size_type as s on c.container_size_type_id= s.id) where b.status='Approved'  and b.port_of_discharge=".$this->requestUrl[2]."  order by sail_date desc limit 20"; // will update after wednewday sept 22
    $this->addActionButtons='No';
    break;
  case 'manifest':         
      $sql="SELECT * FROM `bill_of_lading` where booking_id=".$this->requestUrl[2]." order by parent_bol desc"; 
      $this->addActionButtons='No';
      break;
  case 'master':         
    $sql="SELECT * FROM ((((`bill_of_lading` as l) left join booking as b on l.booking_id=b.id) left join booking_container as c on b.id=c.booking_id) left join container_size_type as t on c.container_size_type_id=t.id) where l.parent_bol=1 and l.booking_id=".$this->requestUrl[2];
    break;
  case 'billoflading':         
      $sql="SELECT * FROM bill_of_lading as b where b.parent_bol=0 and b.booking_id=".$this->requestUrl[2]." order by consignee_sname ";
      break;  
  case 'billoflading_detail':         
        $sql="SELECT b.bill_of_lading_number,c.description as commodity_description,p.description as package_description,d.* FROM (((bill_of_lading as b left join bill_of_lading_detail as d on b.id=d.billoflading_id) left join package as p on d.package_type_id=p.id) left join commodity as c on d.commodity_id=c.id) where b.parent_bol=0 and b.booking_id=".$this->requestUrl[2];
        break;      
  case 'billoflading_charge':         
        $mysqli=$this->db->conn;  
        $sql="SELECT data_value from system_values where code='freight_id'";
        $result = mysqli_query($mysqli,$sql);
        $freight= mysqli_fetch_object($result);	        
        $sql="SELECT (c.amount-c.amount_paid) as balance,b.bill_of_lading_number,c.* FROM bill_of_lading as b left join bill_of_lading_other_charge as c on b.id=c.billoflading_id where b.parent_bol=0 and (c.amount-c.amount_paid)>0 and c.charge_item_id=".$freight->data_value." and b.booking_id=".$this->requestUrl[2];
       break;            
  case 'tallyx':         
      $sql="SELECT v.vessel_name,b.manifest_number,b.status,b.id,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM ((booking as b left join port as p on b.port_of_loading=p.id) left join vessel as v on b.vessel_id=v.id) where (b.status is null or b.status <>'Approved') order by b.sail_date ";
      break;   
  case 'DELETE':
      $sql = "delete from xx `$table` where $where"; 
      break;
  }

?>
