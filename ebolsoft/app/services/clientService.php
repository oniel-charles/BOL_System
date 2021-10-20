
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT *,concat(client_fname,' ',client_sname) as description FROM `client` order by client_fname ";
    break;
    case 'excel':    
      $sql="SELECT *,concat(client_fname,' ',client_sname) as description FROM `client` order by client_fname ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
