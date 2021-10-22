<?php
switch ($this->requestUrl[1]) {
  case 'select':         
    $sql="SELECT * FROM `edi_translation` where translation_source_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;
    case 'select':         
    $sql="SELECT * FROM `edi_translation` where translation_source_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    case 'list_view':         
      if ($this->requestUrl[2]=='port'){
        $sql="SELECT e.id,e.internal_code,e.external_code,p.port_name as description FROM `edi_translation` as e left join port as p on e.code_id=p.id WHERE e.type = 'port' and `translation_source_id`= ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
      }
      if ($this->requestUrl[2]=='package'){
        $sql="SELECT e.id,e.internal_code,e.external_code,p.description  FROM `edi_translation` as e  left join package as p on e.code_id=p.id WHERE   e.type = 'package' and `translation_source_id`=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
      } 
      if ($this->requestUrl[2]=='commodity'){
        $sql="SELECT e.id,e.internal_code,e.external_code,c.description  FROM `edi_translation` as e  left join commodity as c on e.code_id=c.id WHERE   e.type = 'commodity' and `translation_source_id`=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
      }
    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

?>
