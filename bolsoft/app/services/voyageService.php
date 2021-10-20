<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT voyage.id,manifest_number,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name,transportation_mode FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by arrival_date desc ";
    $this->addActionButtons='Yes';
    break;
  case 'select':         
    $sql="SELECT voyage.id,manifest_number,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name,transportation_mode FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by arrival_date desc ";
    break; 
  case 'booking':         
      $sql="SELECT booking_id,voyage.id,manifest_number,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name,transportation_mode FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])."  and manifest_number='".$this->requestUrl[3]."'";
      break;   
  case 'port':         
    $sql="select v.*,o.port_name as port_of_origin,d.port_name as port_of_discharge from (((voyage as v left join bill_of_lading as b on v.id=b.voyage_id) left join port as o on b.port_of_origin =o.id) left join port as d on b.port_of_discharge=d.id) where b.parent_bol=1 and v.id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;       
  case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
