<?php
switch ($request[2]) {
  case 'select':         
    $sql="SELECT * FROM `edi_translation` where translation_source_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
    break;
    case 'select':         
    $sql="SELECT * FROM `edi_translation` where translation_source_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
    case 'list_view':         
      if ($request[3]=='port'){
        $sql="SELECT e.id,e.internal_code,e.external_code,p.port_name as description FROM `edi_translation` as e left join port as p on e.code_id=p.id WHERE e.type = 'port' and `translation_source_id`= ".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
      }
      if ($request[3]=='package'){
        $sql="SELECT e.id,e.internal_code,e.external_code,p.description  FROM `edi_translation` as e  left join package as p on e.code_id=p.id WHERE   e.type = 'package' and `translation_source_id`=".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
      } 
      if ($request[3]=='commodity'){
        $sql="SELECT e.id,e.internal_code,e.external_code,c.description  FROM `edi_translation` as e  left join commodity as c on e.code_id=c.id WHERE   e.type = 'commodity' and `translation_source_id`=".preg_replace('/[^a-z0-9_]+/i','',$request[4]);
      }
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
