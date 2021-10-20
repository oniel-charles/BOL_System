
<?php
switch ($request[2]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id, description FROM `package` order by description ";
    break;
    case 'search':    
    $search_parm="";
    if(isset($_POST['q'])){$search_parm=$_POST['q'];}
    $sql = "SELECT id, description from ".$request[0]." where description LIKE '%$search_parm%' LIMIT 20";;
    break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

?>
