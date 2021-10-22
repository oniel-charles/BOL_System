
<?php
echo "i am in appointment";
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id, description FROM `appointment` order by description ";
    break;
    case 'cypher': 
      $pass=createPassWord(179278);
      echo $pass;
      $val=decodePassword($pass);
      
      echo '<br>'.$val;
      exit();
      //$tablesjson->{"address"}->{"pkey"}
      $sql="SELECT id, description FROM `appointment` order by description ";
      break;
    
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

  function createPassWord($bolid){    
    $pass="";
    $bolstr=strval($bolid);
    $cypher = array();
    $cypher["0"]=array('T','O','U','I','L','V','Z','W','H','X','A','R','K','G','F','S','C','B','Q','P','N','M','J','E','D','Y');
    $cypher["1"]=array('H','J','B','A','T','Q','Y','C','R','W','U','X','V','G','P','E','Z','I','N','L','M','S','K','F','D','O');
    for($i=0;$i<strlen($bolstr);$i++){
      $pass=$pass. $cypher[($i % 2)][$bolstr[$i]];
    }
    return  $pass;
  }



?>
