<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {
  case 'list':         
    $sql="SELECT * FROM `bill_of_lading` where booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by id ";
    break;
    case 'master-list':         
    $sql="SELECT * FROM `bill_of_lading` where master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by consignee_name desc ";
    break; 
    case 'select':         
    $sql="SELECT * FROM `bill_of_lading` where booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by consignee_name desc ";
    break; 
    case 'voyage':         
      $sql="SELECT * FROM `bill_of_lading` where voyage_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
      break; 
    case 'house':         
    $sql="SELECT * FROM `bill_of_lading` where parent_bol=0 and booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by consignee_name desc ";
    break; 
    case 'master-select':         
    $sql="SELECT id,bill_of_lading_number,`port_of_origin`,`port_of_loading`,`port_of_discharge`,`port_of_delivery`,`currency_id`  FROM `bill_of_lading` where parent_bol=1 and  booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." order by bill_of_lading_number ";
    break; 
    case 'master-con':         
    $sql="SELECT bill_of_lading.*,bill_of_lading_container.container_number,container_size_type.size_type_code FROM ((`bill_of_lading` left join bill_of_lading_container on bill_of_lading.id=bill_of_lading_container.billoflading_id) left join container_size_type on bill_of_lading_container.container_size_type_id=container_size_type.id)  where bill_of_lading.id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;   
    case 'bill-chg':         
    $sql="SELECT * FROM `bill_of_lading` as bl left join (select a.billoflading_id, a.amount,a.prepaid_flag from bill_of_lading_other_charge as a left join charge_item as b on a.charge_item_id=b.id  where b.item_code='FC') as t on bl.id=t.billoflading_id  where bl.booking_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and bl.master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    case 'doortodoor': 
      $sql="SELECT data_value from system_values where code='freight_id'";
      $result = mysqli_query($mysqli,$sql);
      $freight= mysqli_fetch_object($result);	 
      $range=" ";
      $asat="";
      if(isset($this->requestUrl[3]) && $this->requestUrl[2]>0){   
         $range=" and v.arrival_date  between ".$this->requestUrl[2]." and ".$this->requestUrl[3];
         $asat="  or r.receipt_date>=".$this->requestUrl[3];
      }else{
        if(isset($this->requestUrl[3]) && $this->requestUrl[2]==0){
          $range=" and v.arrival_date  <= ".$this->requestUrl[3];
          $asat="  or r.receipt_date>=".$this->requestUrl[3];
        }
      }
      $sql="SELECT sum(f.amount) as freight,v.arrival_date,pt.port_code,b.currency_id,sum(c.amount) as amt,b.id, b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number FROM ((((((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join port as pt on b.port_of_origin=pt.id) left join bill_of_lading_other_charge c on b.id=c.billoflading_id) left join (select amount,billoflading_id from bill_of_lading_other_charge  where charge_item_id=$freight->data_value) as f   on b.id=f.billoflading_id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join receipt as r on b.id=r.billoflading_id) WHERE parent_bol<>1  $range and b.customer_type='doortodoor' and (c.charge_item_id is null or c.charge_item_id<>$freight->data_value)   
group by v.arrival_date,pt.port_code,b.currency_id,b.id, b.bill_of_lading_number,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number, b.customer_type 
order by pt.port_code,v.arrival_date,p.bill_of_lading_number,b.bill_of_lading_number";
    break;
    case 'outstanding':        
      $sql="SELECT data_value from system_values where code='freight_id'";
      $result = mysqli_query($mysqli,$sql);
      $freight= mysqli_fetch_object($result);	 
      $range=" ";
      $asat="";
      if(isset($this->requestUrl[3]) && $this->requestUrl[2]>0){   
         $range=" and v.arrival_date  between ".$this->requestUrl[2]." and ".$this->requestUrl[3];
         $asat="  or r.receipt_date>=".$this->requestUrl[3];
      }else{
        if(isset($this->requestUrl[3]) && $this->requestUrl[2]==0){
          $range=" and v.arrival_date  <= ".$this->requestUrl[3];
          $asat="  or r.receipt_date>=".$this->requestUrl[3];
        }
      }
      $sql="SELECT v.arrival_date,pt.port_code,i.currency_id,sum(c.amount) as amt,b.id, b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number FROM ((((((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join port as pt on b.port_of_origin=pt.id) left join bill_of_lading_other_charge c on b.id=c.billoflading_id) left join charge_item as i on c.charge_item_id=i.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join receipt as r on b.id=r.billoflading_id) WHERE parent_bol<>1 $range and  (r.cancelled is null or r.cancelled=0) and ( receipt_processed is null or receipt_processed =0 $asat) and (c.charge_item_id is null or c.charge_item_id<>$freight->data_value)   
group by v.arrival_date,pt.port_code,i.currency_id,b.id, b.bill_of_lading_number,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number 
order by pt.port_code,v.arrival_date,p.bill_of_lading_number,b.bill_of_lading_number"; 
$sql="SELECT sum(f.amount) as freight,v.arrival_date,pt.port_code,b.currency_id,sum(c.amount) as amt,b.id, b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number FROM ((((((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join port as pt on b.port_of_origin=pt.id) left join bill_of_lading_other_charge c on b.id=c.billoflading_id) left join (select amount,billoflading_id from bill_of_lading_other_charge  where charge_item_id=$freight->data_value) as f   on b.id=f.billoflading_id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join receipt as r on b.id=r.billoflading_id) WHERE parent_bol<>1 and  (r.cancelled is null or r.cancelled=0 ) $range and ( receipt_processed is null or receipt_processed =0 $asat) and (c.charge_item_id is null or c.charge_item_id<>$freight->data_value)   
group by v.arrival_date,pt.port_code,b.currency_id,b.id, b.bill_of_lading_number,p.bill_of_lading_number,b.consignee_name,v.manifest_number,v.voyage_number 
order by pt.port_code,v.arrival_date,p.bill_of_lading_number,b.bill_of_lading_number";
//exit($sql);
      /*
      $date_range='';
      if(isset($this->requestUrl[2])){
         $date_range=' and v.arrival_date between '.$this->requestUrl[2].' and '.$this->requestUrl[3];
      }
    $sql="SELECT b.customer_type,b.receipt_processed,b.order_processed,b.notify_name,b.notify_address,'' as container_num,b.consignee_phone_num,b.shipper_name,b.shipper_phone_num,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.manifest_number,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE parent_bol<>1 and ( receipt_processed is null or receipt_processed =0)".$date_range;
    */

    break;
    case 'processing':   
      $date_range='';
      if(isset($this->requestUrl[2])){
         $date_range=' and v.sail_date between '.$this->requestUrl[2].' and '.$this->requestUrl[3];
      }
    $sql="SELECT b.customer_type,b.receipt_processed,b.order_processed,b.notify_sname,b.notify_fname,b.notify_address,'' as container_num,b.consignee_phone_num,b.shipper_fname,b.shipper_sname,b.shipper_phone_num,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_fname,b.consignee_sname,b.consignee_address,b.notify_date,'' as stripped,0 as stripped_date,s.vessel_name,v.manifest_number,v.voyage_number,v.sail_date FROM (((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE parent_bol<>1 and ( receipt_processed is null or receipt_processed =0)".$date_range;
    

    break;
    case 'processing_container':         
      $sql="SELECT b.id,bc.container_number  FROM ((`bill_of_lading` as b  left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join bill_of_lading_container as bc on p.id=bc.billoflading_id)  WHERE b.parent_bol<>1 and ( b.receipt_processed is null or receipt_processed =0)";
      break;
    case 'processing_history':         
    $sql="SELECT v.manifest_number,b.notify_fname,b.notify_sname,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.shipper_fname,b.shipper_sname,b.shipper_address,b.consignee_fname,b.consignee_sname,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.sail_date FROM (((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.sail_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3])." and parent_bol<>1  ";
    break;
    case 'relation':         
      $sql="SELECT bl.id,bl.booking_id,b.vessel_id FROM `bill_of_lading` as bl left join booking as b on bl.booking_id=b.id WHERE bl.id= ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;
    case 'admin_processed':         
    $sql="SELECT b.receipt_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.sail_date FROM (((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3])." and parent_bol<>1 and receipt_processed =2 ";
    break;
    case 'admin_open':         
    $sql="SELECT b.receipt_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.sail_date FROM (((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE v.arrival_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3])." and parent_bol<>1 and (receipt_processed is null or receipt_processed =0)  ";
    break;
    case 'goods':         
      $sql="SELECT * from bill_of_lading_detail where billoflading_id=".$this->requestUrl[2];
      break;
    case 'import_bol':         
      mysqli_autocommit($mysqli,FALSE);
      $mysqli=$this->db->conn;       
//Bill of lading       
       $sql= "INSERT INTO `bill_of_lading` (`id`,  `bill_of_lading_number`, `consignee_name`, `consignee_address`, `consignee_phone_num`, `voyage`, `reported_date`, `vessel`, `status`, `voyage_id`) VALUES ";
       $x=0;
       foreach($this->post_data["bol"] as $x => $y){ 
         if($x>0) {$sql=$sql.",";}
         $sql=$sql."(".$y["id"].", '".$y["bill_of_lading_number"]."', '".$y["consignee_name"]."', '".$y["consignee_address"]."', '".$y["consignee_phone_num"]."', '".$y["voyage_number"]."', ".$y["arrival_date"].", '".$y["vessel_name"]."','open', ".$y["voyage_id"].") ";       
         $voyage_id=$y["voyage_id"];
       }
       $result = mysqli_query($mysqli,$sql);
       if (!$result) { handleRollBack($mysqli);}
//Details
       $sql= "INSERT INTO `bill_of_lading_detail` (`id`, `billoflading_id`, `package_type`, `commodity`, `Description_of_goods`, `number_of_items`) VALUES ";
       $x=0;
       foreach($this->post_data["detail"] as $x => $y){ 
         if($x>0) {$sql=$sql.",";}
         $sql=$sql."(".$y["id"].", '".$y["billoflading_id"]."', '".$y["package"]."', '".$y["commodity"]."', '".$y["Description_of_goods"]."', ".$y["number_of_items"].") ";       
       }
       
       $result = mysqli_query($mysqli,$sql);
       if (!$result) { handleRollBack($mysqli);}
//Charges
       $sql= "INSERT INTO `bill_of_lading_other_charge` (`id`, `charge_item`, `amount`,  `currency`, `billoflading_id`) VALUES ";
       $x=0;
       foreach($this->post_data["charge"] as $x => $y){ 
         if($x>0) {$sql=$sql.",";}
         $sql=$sql."(".$y["id"].", '".$y["charge_item"]."', ".$y["amount"].", '".$y["currency_code"]."', ".$y["billoflading_id"].") ";       
       }
       $result = mysqli_query($mysqli,$sql);
       if (!$result) { handleRollBack($mysqli);}

       mysqli_commit($mysqli);
 //Send SMS Messages     
       include './services/APIClient2.php';   
       $sql="SELECT * from bill_of_lading where voyage_id=$voyage_id" ;
       $bl_data = mysqli_query($mysqli,$sql);	
       foreach($bl_data as $current_rec){
           $phone=str_replace(" ","",$current_rec["consignee_phone_num"]);
           $phone=str_replace("-","",$phone); 
           if(substr($phone, 0, 1)!='1' && strlen($phone)==10){$phone='1'.$phone;}
          $res= sendSMS($phone,$current_rec["bill_of_lading_number"],createPassWord($current_rec["id"]));
          $response=json_decode($res,true);    
          var_dump($response);
          echo "<br> between <br>";
          var_dump($response["error"]["code"]);
       }
       exit();   
      break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
  }

  function handleRollBack($mysqli){
    echo mysqli_error($mysqli);
    mysqli_rollback($mysqli);
    die();
  }

  function sendSMS($phone_number,$ref,$code){
      $baseURL='http://192.168.100.65/dev';
               
      // api endpoint
      $apiendpoint = 'https://api.transmitsms.com/send-sms.json';
      // api key
      $apikey = 'c62a42c5df919032f43d3a0b8929f2df';
      // api secret
      $apisecret = 'leon@chamier';
      // send sms post body
      $post = [
          'from' => 'DENNIS SHIP',
          'to'      => $phone_number,
          'message' => "Your shipment is ready. Please use this link $baseURL/appointment/?ref=$ref&code=$code to make an appointment. "
      ];
      $params = http_build_query($post);

      $ch = curl_init($apiendpoint);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
      curl_setopt( $ch, CURLOPT_POST, 1 );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
      curl_setopt( $ch, CURLOPT_HTTPHEADER,
          [
              'content-type: application/x-www-form-urlencoded',
              'Authorization: Basic ' . base64_encode("$apikey:$apisecret")
          ]
      );

      $res = curl_exec( $ch );
      curl_close( $ch );

      return $res;

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
