
<?php
$baseURL="http://127.0.0.1/dev/appointment";
switch ($request[2]) {
    case 'select':    
    $sql="SELECT id, appointment_date  FROM `appointment` order by appointment_date ";
    break;
    case 'consignee':    
      $sql="SELECT * from appointment where billoflading_id= ".$request[3];
      break;
    case 'day_count':    
        $sql="SELECT count(*) as appointment_count from appointment where appointment_date=".$request[3];
        break;  
    case 'max_appt':
      $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
      $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
      $json = json_decode($result, true);    

      $remote_result =CallAPI('GET',$baseURL.'/app/system_values/code/max_appt/',$user,$json["token"]);
      echo $remote_result;  
      exit();
      break;    
    case 'day_count_online':  
      $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
      $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
      $json = json_decode($result, true);    

      $remote_result =CallAPI('GET',$baseURL.'/app/appointment/day_count/'.$request[3],$user,$json["token"]);
      echo $remote_result;  
      exit();
          break;      
    case 'sms':  
        $post_data = json_decode(file_get_contents('php://input'),true);          
        if($post_data["stripped"]==1){
          sendSMS($post_data["voyage_id"],$mysqli);
        }
        exit();
        break;        
    case 'list_online':
      $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
      $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
      $json = json_decode($result, true);    

      $status="";
      if($request[5]!="all"){ $status=" and status='".$request[5]."'";}
      $remote_result =CallAPI('GET',$baseURL.'/app/appointment/list/'.$request[3]."/".$request[4]."/".$request[5],$user,$json["token"]);
      echo $remote_result;
      //var_dump ($remote_result); 
      //var_dump (json_decode($remote_result, true)); 
      exit();       
      break;  
     case 'get_online':
      $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
      $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
      $json = json_decode($result, true);    

      $remote_result =CallAPI('GET',$baseURL.'/app/appointment/'.$request[3],$user,$json["token"]);
      echo $remote_result;
      //var_dump ($remote_result); 
      //var_dump (json_decode($remote_result, true)); 
      exit();
      case 'delete_online':
        $post_data = json_decode(file_get_contents('php://input'),true);  
        $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
        $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
        $json = json_decode($result, true);    
  
        $remote_result =CallAPI('DELETE',$baseURL.'/app/appointment/'.$request[3],$user,$json["token"]);

        $remote_result =CallAPI('DELETE',$baseURL.'/app/bill_of_lading/'.$request[4],$user,$json["token"]);
        
        echo $remote_result;
        //var_dump ($remote_result); 
        //var_dump (json_decode($remote_result, true)); 
        exit();       
      break;  
      case 'put_online':
        $post_data = json_decode(file_get_contents('php://input'),true);  
        $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
        $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
        $json = json_decode($result, true);    
  
        $remote_result =CallAPI('PUT',$baseURL.'/app/appointment/'.$request[3],$post_data,$json["token"]);
        echo $remote_result;
        //var_dump ($remote_result); 
        //var_dump (json_decode($remote_result, true)); 
        exit();       
      break;  
      case 'post_online':
        $post_data = json_decode(file_get_contents('php://input'),true);  
        $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
        $result =CallAPI('POST',$baseURL.'/app/login',$user,"");    
        $json = json_decode($result, true);    
//bill of lading  
        $sql="select b.*,v.voyage_number,v.arrival_date,s.vessel_name from ((bill_of_lading as b left join voyage v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) where b.parent_bol=0 and  b.id=".$post_data["billoflading_id"];                        
        $result = mysqli_query($mysqli,$sql);
        if (!$result) { http_response_code(404);  die($sql.'<br>'.mysqli_error($mysqli)); }          
        $bols =$result -> fetch_all(MYSQLI_ASSOC);
//bl detail
        $sql="select b.*,p.description as package,c.description as commodity from ((bill_of_lading_detail as b left join package as p on b.package_type_id=p.id) left join commodity as c on b.commodity_id=c.id) where  b.billoflading_id =".$post_data["billoflading_id"];                        
        $result = mysqli_query($mysqli,$sql);
        if (!$result) { http_response_code(404);  die($sql.'<br>'.mysqli_error($mysqli)); }          
        $details =$result -> fetch_all(MYSQLI_ASSOC);
//charges
        $sql="select b.*,c.description as charge_item,f.currency_code from ((bill_of_lading_other_charge as b left join charge_item as c on b.charge_item_id =c.id) left join currency as f on b.currency_id=f.id) where   b.billoflading_id =".$post_data["billoflading_id"];                        
        $result = mysqli_query($mysqli,$sql);
        if (!$result) { http_response_code(404);  die($sql.'<br>'.mysqli_error($mysqli)); }          
        $charges =$result -> fetch_all(MYSQLI_ASSOC);

        $data =array("bol" => $bols,"detail"=>$details, "charge" => $charges, "appointment"=>$post_data );   

        $remote_result =CallAPI('POST',$baseURL.'/app/appointment/new_appointment',json_encode($data),$json["token"]);
        echo $remote_result;
        //var_dump ($remote_result); 
        //var_dump (json_decode($remote_result, true)); 
        exit();       
      break;  
     case 'list':  
            $sql="SELECT a.id,a.billoflading_id,a.trn,a.uba_code,a.status,a.appointment_date,b.receipt_processed,b.bill_of_lading_number,b.consignee_name FROM appointment as a left join bill_of_lading as b on a.billoflading_id=b.id where a.appointment_date between ".$request[3]." and ".$request[4].$status;
            break;  
        
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }

  function sendSMS($voyageId,$mysqli){
    $baseURL="http://127.0.0.1/dev/appointment";
    $user =array("user_name" => "BLTZ","password" => base64_encode(createPassWord(1234)));                
    $result =CallAPI('POST',$baseURL.'/app/login',$user,"");
    //var_dump($result);
    $json = json_decode($result, true);
    //var_dump($json);
    $remote_bl =CallAPI('GET',$baseURL.'/app/bill_of_lading/voyage/'.$voyageId,$user,$json["token"]);
    $remote_bls =json_decode($remote_bl,true);    
    if(count($remote_bls)==0){

          $sql="select b.*,v.voyage_number,v.arrival_date,s.vessel_name from ((bill_of_lading as b left join voyage v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) where b.parent_bol=0 and  b.voyage_id=".$voyageId;                        
          $result = mysqli_query($mysqli,$sql);
          if (!$result) { http_response_code(404);  die($sql.'<br>'.mysqli_error($mysqli)); }          
          $bols =$result -> fetch_all(MYSQLI_ASSOC);

          $sql="select b.*,p.description as package,c.description as commodity from ((bill_of_lading_detail as b left join package as p on b.package_type_id=p.id) left join commodity as c on b.commodity_id=c.id) where  b.billoflading_id in (select id from bill_of_lading where parent_bol=0 and voyage_id=$voyageId)";                        
          $result = mysqli_query($mysqli,$sql);
          if (!$result) { http_response_code(404);  die($sql.'<br>'.mysqli_error($mysqli)); }          
          $details =$result -> fetch_all(MYSQLI_ASSOC);

          $sql="select b.*,c.description as charge_item,f.currency_code from ((bill_of_lading_other_charge as b left join charge_item as c on b.charge_item_id =c.id) left join currency as f on b.currency_id=f.id) where   b.billoflading_id in (select id from bill_of_lading where parent_bol=0 and voyage_id=$voyageId)";                        
          $result = mysqli_query($mysqli,$sql);
          if (!$result) { http_response_code(404);  die($sql.'<br>'.mysqli_error($mysqli)); }          
          $charges =$result -> fetch_all(MYSQLI_ASSOC);

          $data =array("bol" => $bols,"detail"=>$details, "charge" => $charges);                

          $remote_result =CallAPI('POST',$baseURL.'/app/bill_of_lading/import_bol',json_encode($data),$json["token"]);
          var_dump ($remote_result);
    }
    /*
    //
    $sql="UPDATE  `bill_of_lading` set value_currency=2,value_of_goods=".number_format($bl_values[$x][0]['amount'],0)." where id=".$bl_values[$x][0]['billoflading_id']." and (value_of_goods is null or value_of_goods=0)";            
            
    $update_result = mysqli_query($mysqli,$sql);
    if (!$update_result) {
      http_response_code(404);
      die($sql.'<br>'.mysqli_error($mysqli));
    }
    */

    echo "here ";
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

  function CallAPI($method, $url, $data = false,$token)
  {
      $curl = curl_init();
  
      switch ($method)
      {
          case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
  
              if ($data)
                  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
          case "PUT":
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);            
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen(json_encode($data))));
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);        
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
           break;
          default:
              if ($data)
                  $url = sprintf("%s?%s", $url, http_build_query($data));
      }
  
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($curl, CURLOPT_USERAGENT, "PHP-ADMIN");
      // Optional Authentication:
      //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      //curl_setopt($curl, CURLOPT_USERPWD, "username:password");
      //Set your auth headers
      if($token){
          curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Authorization: Bearer ' . $token
          ));
      }
  
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  
      $result = curl_exec($curl);
  
      curl_close($curl);
  
      return $result;
  }
  
  function handleRollBack($mysqli,$sql){
    echo mysqli_error($mysqli);
    mysqli_rollback($mysqli);
    http_response_code(404);
    die($sql);
  }
?>
