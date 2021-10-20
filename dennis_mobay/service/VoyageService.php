<?php
switch ($request[2]) {
  case 'list':         
    $sql="SELECT voyage.id,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by arrival_date desc ";
    break;
    case 'select':         
    $sql="SELECT voyage.id,vessel_code,departure_date,arrival_date,voyage_number,stripped,stripped_date,lloyd_number,vessel_name FROM `voyage` left join vessel on voyage.vessel_id=vessel.id where vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3])." order by arrival_date desc ";
    break; 
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
