
<?php
switch ($request[0]) {  
  case 'delete':
      $sql = " delete some thing"; 
      break;
  case 'load':
      //validate user acccess to option
     // header("HTTP/1.1 401 Unauthorized");
     // exit();
      break;
  }

?>
