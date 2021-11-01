
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT id, description FROM `appointment` order by description ";
    break;
    case 'consignee':    
      $sql="SELECT * from appointment where billoflading_id= ".$request[3];
      break;
    case 'day_count':    
        $sql="SELECT 0 as id,count(*) as appointment_count from appointment where appointment_date=".$this->requestUrl[2];
        break;  
    case 'list':    
      $status="";
      //if($this->requestUrl[4]!="all"){ $status=" and status='".$this->requestUrl[4]."'";}
        $sql="SELECT a.id,a.billoflading_id,a.trn,a.uba_code,a.status,a.appointment_date,b.receipt_processed,b.bill_of_lading_number,b.consignee_name FROM appointment as a left join bill_of_lading as b on a.billoflading_id=b.id where a.appointment_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3].$status;
        break;  
    case 'bol':    
      //$tablesjson->{"address"}->{"pkey"}
      $sql="select * from appointment where billoflading_id=".$this->requestUrl[2];
      $this->addActionButtons='No';
      break;
    case 'cypher': 
      $pass=createPassWord($this->requestUrl[2]);
      echo $pass;
      $val=decodePassword($pass);
      
      echo '<br>'.$val;
      exit();
      //$tablesjson->{"address"}->{"pkey"}
      $sql="SELECT id, description FROM `appointment` order by description ";
      break;
    case 'new_appointment': 
      $mysqli=$this->db->conn;       
      mysqli_autocommit($mysqli,FALSE);
         
//Bill of lading       
       $sql= "INSERT INTO `bill_of_lading` (`id`,  `bill_of_lading_number`, `consignee_name`, `consignee_address`, `consignee_phone_num`, `voyage`, `reported_date`, `vessel`, `status`, `voyage_id`) VALUES ";
       $x=0;
       foreach($this->post_data["bol"] as $x => $y){ 
         if($x>0) {$sql=$sql.",";}
         $sql=$sql."(".$y["id"].", '".$y["bill_of_lading_number"]."', '".$y["consignee_name"]."', '".$y["consignee_address"]."', '".$y["consignee_phone_num"]."', '".$y["voyage_number"]."', ".$y["arrival_date"].", '".$y["vessel_name"]."','open', ".$y["voyage_id"].") ";       
         $voyage_id=$y["voyage_id"];
       }
       $result = mysqli_query($mysqli,$sql);
       if (!$result) { handleRollBack($mysqli,$sql);}
//Details
       $sql= "INSERT INTO `bill_of_lading_detail` (`id`, `billoflading_id`, `package_type`, `commodity`, `Description_of_goods`, `number_of_items`) VALUES ";
       $x=0;
       foreach($this->post_data["detail"] as $x => $y){ 
         if($x>0) {$sql=$sql.",";}
         $sql=$sql."(".$y["id"].", '".$y["billoflading_id"]."', '".$y["package"]."', '".$y["commodity"]."', '".$y["Description_of_goods"]."', ".$y["number_of_items"].") ";       
         $x++;
       }
       if($x>0){
          $result = mysqli_query($mysqli,$sql);
          if (!$result) { handleRollBack($mysqli,$sql);}
       }
//Charges
       $sql= "INSERT INTO `bill_of_lading_other_charge` (`id`, `charge_item`, `amount`,  `currency`, `billoflading_id`) VALUES ";
       $x=0;
       foreach($this->post_data["charge"] as $x => $y){ 
         if($x>0) {$sql=$sql.",";}
         $sql=$sql."(".$y["id"].", '".$y["charge_item"]."', ".$y["amount"].", '".$y["currency_code"]."', ".$y["billoflading_id"].") ";       
         $x++;
       }
       if($x>0){
          $result = mysqli_query($mysqli,$sql);
          if (!$result) { handleRollBack($mysqli,$sql);}
       }
//Appointment
      $sql= "INSERT INTO `appointment` ( `billoflading_id`, `trn`, `uba_code`, `passport_number`, `status`, `appointment_date`) VALUES  ";
      $sql=$sql."(".$this->post_data["appointment"]["billoflading_id"].", '".$this->post_data["appointment"]["trn"]."', '".$this->post_data["appointment"]["uba_code"]."', '".$this->post_data["appointment"]["passport_number"]."', '".$this->post_data["appointment"]["status"]."', ".$this->post_data["appointment"]["appointment_date"].") ";       
      $result = mysqli_query($mysqli,$sql);
      echo (mysqli_insert_id($mysqli));
       if (!$result) { handleRollBack($mysqli,$sql);}       
       mysqli_commit($mysqli);        
       exit();
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


  function handleRollBack($mysqli,$sql){
    echo $sql;
    echo mysqli_error($mysqli);
    mysqli_rollback($mysqli);
    die();
  }


?>
