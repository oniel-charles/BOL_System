
<?php
$where='';
switch ($this->requestUrl[1]) {
    case 'select':    
    $sql="SELECT id,user_name as description from user_profile order by user_name ";
    break;
    case 'excel':    
    $sql="select id,user_name,full_name,status from user_profile order by user_name ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$this->table` where $where"; 
      break;
  }
  
?>
