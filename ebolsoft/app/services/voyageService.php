<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT voyage.id,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name,transportation_mode FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by arrival_date desc ";
    $this->addActionButtons='Yes';
    break;
  case 'select':         
    $sql="SELECT voyage.id,manifest_number,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name,transportation_mode FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by arrival_date desc ";
    break; 
  case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
