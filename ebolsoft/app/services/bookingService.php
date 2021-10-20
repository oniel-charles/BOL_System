<?php
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT b.stripped,b.stripped_date,b.id,b.manifest_number,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM booking as b left join port as p on b.port_of_loading=p.id where b.vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by sail_date desc ";
    $this->addActionButtons='No';
    break;
  case 'select':         
    $sql="SELECT b.status,b.id,b.manifest_number,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM booking as b left join port as p on b.port_of_loading=p.id where b.status<>'Approved' and b.vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by sail_date desc ";
    break;
  case 'select_approve':         
      $sql="SELECT b.status,b.id,b.manifest_number,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM booking as b left join port as p on b.port_of_loading=p.id where (b.stripped is null or b.stripped=0) and b.vessel_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by sail_date desc ";
      break;  
  case 'tally':         
      $sql="SELECT v.vessel_name,b.manifest_number,b.status,b.id,b.voyage_number,b.sail_date,b.document_number,p.port_name as port_of_loading FROM ((booking as b left join port as p on b.port_of_loading=p.id) left join vessel as v on b.vessel_id=v.id) where (b.status is null or b.status <>'Approved') order by b.sail_date ";
      break;   
  case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
