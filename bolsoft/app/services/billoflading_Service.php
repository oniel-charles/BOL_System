<?php
$mysqli=$this->db->conn;  
if (!isset($service_type)){
  $service_type=$this->requestUrl[1];
}

switch ($service_type) {
  case 'charges':         
  date_default_timezone_set('America/Jamaica');
  $cur_date= date('Ymd') ;

  //Get Freight ID
  $sql="SELECT data_value from system_values where code='freight_id'";
  $result = mysqli_query($mysqli,$sql);
  $freight= mysqli_fetch_object($result);	
  //$freight->data_value

  // get bl info
   $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.manifest_number,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".intval($bl_id);
   $result = mysqli_query($mysqli,$sql);
   $bill_rec= mysqli_fetch_object($result);	

   
  // GCT rate
  $sql="SELECT * from charge_item_rate where charge_item_id=-1 and effective_date <=".$cur_date."  order by effective_date desc limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $gct_rec= mysqli_fetch_object($result);	

  
  // SUR CHARGE
  $sql="SELECT * from charge_item where id=-2  limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $sur_rec= mysqli_fetch_object($result);	

  // EXchange Rate
  $sql="SELECT * from currency_rate where currency_id=2 and effective_date <=".$cur_date." order by effective_date desc limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $us_rate= mysqli_fetch_object($result);	

  $user_name=$claims['user'];//$claims['full_name'];
  $gct_total=0;
  $receipt_total=0;
  $currency_total=0;
  $service_result = array();
  // GET DETAIL EITHER FROM RECEIPT OR FROM BILL OF LATING FOR RECEIPTS 

  
  //$sql="SELECT x.*,b.amount from (SELECT r.billoflading_id,d.charge_item_id,u.user_name,u.full_name,r.id,r.receipt_date,r.receipt_time,c.description,d.currency_amount as paid FROM (((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id)  where (r.cancelled=0 or r.cancelled is null) and r.billoflading_id=".intval($bl_id)." ) as x left join bill_of_lading_other_charge as b on x.billoflading_id=b.billoflading_id and x.charge_item_id=b.charge_item_id where x.paid=b.amount";
  //$sql="SELECT x.*,b.amount from (SELECT r.billoflading_id,d.charge_item_id,u.user_name,u.full_name,r.id,r.receipt_date,r.receipt_time,c.description,sum(d.currency_amount) as paid FROM (((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where (r.cancelled=0 or r.cancelled is null) and d.charge_item_id>0 and r.billoflading_id=".intval($bl_id)." group by r.billoflading_id,d.charge_item_id,u.user_name,u.full_name,r.receipt_date,r.receipt_time,c.description) as x left join bill_of_lading_other_charge as b on x.billoflading_id=b.billoflading_id and x.charge_item_id=b.charge_item_id where x.paid=b.amount"; 
  $sql="SELECT x.*,b.amount from (SELECT r.id,r.receipt_date,r.customer_identification,r.billoflading_id,d.charge_item_id,u.user_name,u.full_name,c.description,sum(d.currency_amount) as paid,sum(d.discount) discounted  FROM (((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where (r.cancelled=0 or r.cancelled is null) and d.charge_item_id>0 and r.billoflading_id=".intval($bl_id)." group by c.description) as x left join bill_of_lading_other_charge as b on x.billoflading_id=b.billoflading_id and x.charge_item_id=b.charge_item_id where (x.paid+x.discounted)=b.amount";
  $charges_paid = mysqli_query($mysqli,$sql);
  if (!$charges_paid) {
    http_response_code(404);
    die($sql.'<br>'.mysqli_error($mysqli));
  }
  //echo mysqli_num_rows($charges_paid)."PAID  <br>".$sql."<br><br>";    
  //$sql="SELECT b.attract_gct,c.currency_id as item_currency, b.currency_id as bill_currency ,b.prepaid_flag,b.charge_item_id,'' as user_name, '' as full_name,0 id, 0 as receipt_date, 0 as receipt_time,c.description,b.amount,r.currency_amount as paid,r.currency_amount FROM ((`bill_of_lading_other_charge` as b left join (select dt.id, dt.charge_item_id,dt.bol_id,dt.currency_amount from receipt as hd left join receipt_detail as dt on hd.id=dt.receipt_id where (hd.cancelled=0 or hd.cancelled is null) and hd.billoflading_id=1021 ) as r on b.charge_item_id=r.charge_item_id and b.billoflading_id=r.bol_id ) left join charge_item as c on b.charge_item_id=c.id) where  b.prepaid_flag='c' and b.billoflading_id =".intval($bl_id) ." and (r.currency_amount is null or b.amount> r.currency_amount)";
  //$sql="SELECT x.id,x.receipt_date,x.receipt_time,x.paid,b.charge_item_id, b.billoflading_id,c.currency_id as item_currency,b.amount,c.description,b.attract_gct, b.currency_id as bill_currency from ((bill_of_lading_other_charge as b left join (SELECT r.billoflading_id,d.charge_item_id,u.user_name,u.full_name,r.id,r.receipt_date,r.receipt_time,sum(d.currency_amount) as paid FROM ((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join user_profile as u on r.created_by=u.id) where (r.cancelled=0 or r.cancelled is null) and d.charge_item_id>0 and r.billoflading_id=".intval($bl_id) ." group by r.billoflading_id,d.charge_item_id,u.user_name,u.full_name,r.receipt_date,r.receipt_time) as x on b.billoflading_id=x.billoflading_id and b.charge_item_id=x.charge_item_id) left join charge_item as c on b.charge_item_id=c.id) where b.billoflading_id=".intval($bl_id) ." and b.prepaid_flag='c' and ( x.paid is null or x.paid < b.amount)";
  $sql="SELECT  d.amount as discount,d.basis,x.paid,x.discounted,b.charge_item_id,b.billoflading_id,c.currency_id as item_currency,b.amount,c.description,b.attract_gct, b.currency_id as bill_currency from (((bill_of_lading_other_charge as b left join (SELECT r.billoflading_id,d.charge_item_id,sum(d.discount) as discounted,sum(d.currency_amount) as paid FROM (`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) where (r.cancelled=0 or r.cancelled is null) and d.charge_item_id>0 and r.billoflading_id=".intval($bl_id) ." group by r.billoflading_id,d.charge_item_id) as x on b.billoflading_id=x.billoflading_id and b.charge_item_id=x.charge_item_id) left join charge_item as c on b.charge_item_id=c.id) left join billoflading_discount as d on b.billoflading_id=d.billoflading_id and b.charge_item_id=d.charge_item_id) where b.billoflading_id=".intval($bl_id) ." and b.prepaid_flag='c' and ( x.paid is null or (x.discounted+x.paid) < b.amount )";
  $charges_unpaid = mysqli_query($mysqli,$sql); 
  //echo $sql; 
  if (!$charges_unpaid) {
    http_response_code(404);
    die($sql.'<br>'.mysqli_error($mysqli));
  }
  
       $new_receipt=true;
       
       $receipt_array = array('fc_jm' => array(),'fc_us' => array(), 'jm' => array());

      //Freight Charges
      $rec_id=0;    
      $total_discount=0;
      for ($i=0;$i<mysqli_num_rows($charges_unpaid);$i++) {
          $rec= mysqli_fetch_object($charges_unpaid);	 
          if ($freight->data_value != $rec->charge_item_id) continue;
          $discount_amount=0;
          $discounted=$rec->discounted;
          if ($rec->basis=='$'){
             $discount_amount= $rec->discount;
           }
           if ($rec->basis=='%'){
            $discount_amount= round(($rec->amount/100)*$rec->discount,2);
           }
            $currency_amount=$rec->amount-floatval($rec->paid)-$discount_amount;
            if ($post_data["us_amount"] > $currency_amount) {
              http_response_code(428);
              die($post_data["us_amount"]." The amount entered exceed the amount owing ".$currency_amount);
            }  
            $us_amount=floatval($post_data["us_amount"]);
            $jm_amount= round(($currency_amount-$us_amount) * $us_rate->exchange_rate,2);  
            $total_discount+=$discount_amount;
            $discount_amount=$discount_amount-$discounted;
            if ($us_amount>0){              
              $paid=floatval($rec->paid)+$discounted;
              $currency_amount=$us_amount;
              $receipt_array["fc_us"] =array('details' => array(),'currency' =>2, 'total' =>$us_amount, 'total_discount' =>$total_discount);
              $receipt_array["fc_us"]["details"][] = array("discount"=>$discount_amount,"bill_amount"=>$rec->amount,"rec_date"=>$rec->receipt_date,"rec_id"=>$rec->id,"paid"=>$paid,"roe"=>$us_rate->exchange_rate,"currency_amount"=>$currency_amount,"receipt_number"=>0,"date"=>0,"item"=>$rec->charge_item_id,"bill_currency"=>$rec->bill_currency,"item_currency"=>$rec->item_currency, "gct"=> $rec->attract_gct,"description"=> $rec->description, "amount" => $us_amount); 
             /* if ($rec->attract_gct=="1") {
                  $gct_total = round($us_amount *.01 * $gct_rec->rate,2);
                  $receipt_array["fc_us"]["details"][] = array("currency_amount"=>0,"receipt_number"=>0,"date"=>0,"item"=>-1,"bill_currency"=>2, "gct"=>0,"description"=> "GCT", "amount" => $gct_total);
              }*/
              $discounted +=$discount_amount;
              $discount_amount=0;
            }            
            if ($jm_amount>0){
              $paid=floatval($rec->paid)+$discounted;
              $currency_amount=$rec->amount-$paid-$us_amount-$discount_amount;
              $receipt_array["fc_jm"] =array('details' => array(),'currency' =>1, 'total' =>$jm_amount, 'total_discount' =>$discount_amount);
              $receipt_array["fc_jm"]["details"][] = array("discount"=>$discount_amount,"bill_amount"=>$rec->amount,"paid"=>$paid,"roe"=>$us_rate->exchange_rate,"currency_amount"=>$currency_amount,"receipt_number"=>0,"date"=>0,"item"=>$rec->charge_item_id,"bill_currency"=>$rec->bill_currency,"item_currency"=>$rec->item_currency, "gct"=> $rec->attract_gct,"description"=> $rec->description, "amount" => $jm_amount); 
            }
            break;
      }

      $charges_unpaid->data_seek(0);
      //JMD Receipts
      $roe_str="";
      $rec_id=0;
      $receipt_total=0;
      $total_discount=0;
      $receipt_array["jm"] =array('details' => array(),'currency' =>1, 'total' =>0,'total_discount'=>0); 
      if($print_separate==false && count($receipt_array["fc_jm"]["details"])>0){
        //$receipt_array["jm"]["details"][]= $receipt_array["fc_jm"]["details"];
        $receipt_array["jm"]["details"][]=new ArrayObject($receipt_array["fc_jm"]["details"][0]);
        //array_merge($receipt_array["jm"]["details"],$receipt_array["fc_jm"]["details"]);
        unset($receipt_array["fc_jm"]["details"]);
      }   
     //var_dump($receipt_array);
       //   exit();
      for ($i=0;$i<mysqli_num_rows($charges_unpaid);$i++) {
          $rec= mysqli_fetch_object($charges_unpaid);	
          if ($freight->data_value == $rec->charge_item_id) continue;
          $discount_amount=0;
          if ($rec->basis=='$'){
             $discount_amount= $rec->discount;
           }
           if ($rec->basis=='%'){
            $discount_amount= ($rec->amount/100)*$rec->discount;
           }
            $desc=$rec->description;            
            $currency_amount=$rec->amount-$discount_amount;
            $amount = $rec->amount-$discount_amount ;              
            if ($rec->item_currency==2 && $rec->bill_currency==1){                      
              $amount = round(($rec->amount-$discount_amount) * $us_rate->exchange_rate,2);              
            }
            
                   
            $receipt_array["jm"]["details"][] = array("discount"=>$discount_amount,"bill_amount"=>$rec->amount,"paid"=>$rec->paid,"roe"=>$us_rate->exchange_rate,"currency_amount"=>$currency_amount,"receipt_number"=>0,"date"=>0,"item"=>$rec->charge_item_id,"bill_currency"=>$rec->bill_currency,"item_currency"=>$rec->item_currency, "gct"=> $rec->attract_gct,"description"=> $rec->description, "amount" => $amount); 
            $receipt_total +=$rec->amount;    
            if ($rec->attract_gct=="1") {$gct_total+=$amount;} 
             $total_discount+=$discount_amount; 
      }      
      if ($gct_total>0){
        $gct_total = round($gct_total *.01 * $gct_rec->rate,2);
        $receipt_array["jm"]["details"][] = array("discount"=>0,"bill_amount"=>$gct_total,"paid"=>0,"currency_amount"=>0,"receipt_number"=>0,"date"=>0,"item"=>-1,"bill_currency"=>1, "gct"=>0,"description"=> "GCT", "amount" => $gct_total);
        $receipt_total +=$gct_total;
      }
      $receipt_array["jm"]["total"]=$receipt_total;
      $receipt_array["jm"]["total_discount"]=$total_discount;
      $service_result =array();
      if ($receipt_array["jm"]["details"])    $service_result= array_merge($service_result,$receipt_array["jm"]["details"]);
      if ($receipt_array["fc_jm"]["details"]) $service_result= array_merge($service_result,$receipt_array["fc_jm"]["details"]);
      if ($receipt_array["fc_us"]["details"]) $service_result= array_merge($service_result,$receipt_array["fc_us"]["details"]);

 

      if ( $request[1]!='create'){        
        for ($i=0;$i<mysqli_num_rows($charges_paid);$i++) {         
          $rec= mysqli_fetch_object($charges_paid);	                     
          $service_result[] = array("discount"=>round($rec->discounted,2),"identification"=>$rec->customer_identification,"bill_amount"=>$rec->amount,"paid"=>$rec->paid,"currency_amount"=>0,"paid"=>$rec->paid,"item"=>$rec->charge_item_id,"receipt_number"=>$rec->id,"date"=>$rec->receipt_date,"time"=>$rec->receipt_time,"bill_currency"=>0, "gct"=>0,"description"=> $rec->description, "amount" => $rec->amount);      
        }
      }
      /*
      var_dump($receipt_array);
      echo "<br><br>";
      var_dump($service_result);
      exit();
      */
    break;
    case 'reprint':   
    $total_discount;     
    $bl_id=0;
    $sql="SELECT r.payment_type_no,r.payment_type,c.currency_id as item_currency,r.exchange_rate,r.currency_id as bill_currency,r.billoflading_id,d.currency_amount,d.charge_item_id,u.user_name,u.full_name,r.id,r.receipt_date,r.receipt_time,c.description,d.discount,d.amount,d.amount as paid FROM (((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where (r.cancelled=0 or r.cancelled is null) and r.id=".intval($rec_id);
    $result = mysqli_query($mysqli,$sql);
    for ($i=0;$i<mysqli_num_rows($result);$i++) {           
         $rec= mysqli_fetch_object($result);	
         $payment_type=$rec->payment_type;
         $payment_type_no=$rec->payment_type_no;
         $bl_id=$rec->billoflading_id;                  
         $receipt_currencey=$rec->currency_id;
         $user_name=$rec->user_name;//$rec->full_name;         
         if($rec->item_currency==2){
           $roe_str='R.O.E @'.$rec->exchange_rate;           
         }
         if ($rec->bill_currency==2){
          $currency_code='USD';
         }
         $total_discount+=$rec->discount;
         $service_result[] = array("discount"=>$rec->discount,"currency_amount"=>$rec->currency_amount,"item"=>$rec->charge_item_id,"receipt_number"=>$rec->id,"date"=>$rec->receipt_date,"time"=>$rec->receipt_time,"bill_currency"=>$rec->bill_currency,"item_currency"=>$rec->item_currency, "gct"=>0,"description"=> $rec->description, "amount" => $rec->amount);      
    }
    $receipt_array = array('reprint' => array());
    $receipt_array["reprint"] =array('details' => $service_result,'currency' =>0, 'total' =>0,'total_discount'=>$total_discount);        
  // get bl info
   $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.manifest_number,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".intval($bl_id);
   $result = mysqli_query($mysqli,$sql);
   $bill_rec= mysqli_fetch_object($result);	

    break;
    case 'select':         
    $sql="SELECT b.*,currency_code FROM `bill_of_lading_other_charge` as b left join currency on b.currency_id=currency.id WHERE b.billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
    break;
    case 'update_house':         
        $sql="SELECT * from bill_of_lading where id=".preg_replace('/[^a-z0-9_]+/i','',$request[2]);
        echo "<br><br>".$sql;
        $result = mysqli_query($mysqli,$sql);
        if (!$result) {
          http_response_code(404);
          die($sql.'<br>'.mysqli_error($mysqli));
        }
        $bl_rec= mysqli_fetch_object($result);	
        $sql="update bill_of_lading set port_of_origin=".$bl_rec->port_of_origin.",port_of_loading=".$bl_rec->port_of_loading.",port_of_discharge=".$bl_rec->port_of_discharge.",port_of_delivery=".$bl_rec->port_of_delivery.",currency_id=".$bl_rec->currency_id." where master_bol_id=".preg_replace('/[^a-z0-9_]+/i','',$request[2]);
        echo "<br><br>".$sql;
      $upd_result = mysqli_query($mysqli,$sql);
      if (!$upd_result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }else{
         echo mysqli_affected_rows($mysqli);
      }   

    break;
    case 'DELETE':
      $sql = "delete from `$table` where $where"; 
      break;
    case 'apply_charge':  
        // SUR CHARGE
      $charge_id=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);  
      $voyage_id=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
      $sql="SELECT * from charge_item where id=".$charge_id."  limit 1 " ;
      $result = mysqli_query($mysqli,$sql);
      $item_rec= mysqli_fetch_object($result);	   

      $sql="SELECT id FROM bill_of_lading where id not in (SELECT b.id FROM bill_of_lading  as b left join `bill_of_lading_other_charge` as c on b.id=c.billoflading_id WHERE b.voyage_id=".$voyage_id." and c.charge_item_id=".$charge_id.") and voyage_id=".$voyage_id." and (parent_bol is null or parent_bol=0) and (receipt_processed is null or receipt_processed=0)" ;
      $result = mysqli_query($mysqli,$sql);
     // echo "number of rows".mysqli_num_rows($result)."\n";
      $sql = " INSERT INTO `bill_of_lading_other_charge` ( `charge_item_id`, `amount`, `prepaid_flag`, `attract_gct`, `currency_id`, `billoflading_id`) VALUES ";
      $comma='';
      for ($i=0;$i<mysqli_num_rows($result);$i++) {
           $rec= mysqli_fetch_object($result);	           
           $sql =$sql.$comma. "  (".$charge_id.",".$item_rec->item_rate.",'c',".$item_rec->gct.",".$item_rec->currency_id.",".$rec->id.")";         
           $comma=',';
      }        
      $upd_result = mysqli_query($mysqli,$sql);
      if (!$upd_result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }
      echo mysqli_num_rows($result);
      exit();
      break;
    case 'unapply_charge':          
        $charge_id=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);  
        $voyage_id=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
        $sql=" delete from  bill_of_lading_other_charge where charge_item_id=".$charge_id." and  billoflading_id in (SELECT b.id FROM bill_of_lading as b left join (select * from `bill_of_lading_other_charge`) as c on b.id=c.billoflading_id WHERE b.voyage_id=$voyage_id and c.charge_item_id=$charge_id and (b.receipt_processed is null or b.receipt_processed=0))";
        $result = mysqli_query($mysqli,$sql);        
        if (!$result) {
          http_response_code(404);
          die($sql.'<br>'.mysqli_error($mysqli));
        }
        echo mysqli_affected_rows($mysqli);
        exit();
        break;    
     case 'admin_update_assign':         
          $ids=implode(",",$this->post_data['ids']);

          $sql="update bill_of_lading set receipt_processed=2 where (receipt_processed=0 or receipt_processed is null) and id in (".$ids.") ";
          $upd_result = mysqli_query($mysqli,$sql);
          if (!$upd_result) {
            http_response_code(404);
            die($sql.'<br>'.mysqli_error($mysqli));
          }else{
            echo mysqli_affected_rows($mysqli);
          }
          break;  
      case 'admin_update_undo':         
          $ids=implode(",",$this->post_data['ids']);

          $sql="update bill_of_lading set receipt_processed=0 where receipt_processed=2 and id in (".$ids.") ";
          echo $sql;
          $upd_result = mysqli_query($mysqli,$sql);
          if (!$upd_result) {
            http_response_code(404);
            die($sql.'<br>'.mysqli_error($mysqli));
          }else{
            echo mysqli_affected_rows($mysqli);
          }
          break;
      case 'generate_charge':        
            $bl_charges=  array();
            $bl_values=  array();
            $key='';
            $voyage_id=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
            $sql="select * from rate_table  ";
            $rates = mysqli_query($mysqli,$sql);        
            for ($i=0;$i<mysqli_num_rows($rates);$i++) {              
              $rate= mysqli_fetch_object($rates);	  
              $sql="SELECT * FROM `bill_of_lading_detail`  WHERE  billoflading_id in (select id from bill_of_lading where voyage_id=".$voyage_id.") and package_type_id=".$rate->package_id." order by billoflading_id";
              $details = mysqli_query($mysqli,$sql);                  
              for ($x=0;$x<mysqli_num_rows($details);$x++) {
                $detail= mysqli_fetch_object($details);	
                $key=$detail->billoflading_id.':'.$rate->charge_item_id;
                $amount=0;
                if($rate->basis=='quantity'){
                  //$amount=$rate->rate*$detail->number_of_items;
                  $amount=calculateQuanCharge($rate->rate,$detail->number_of_items);
                }  
                if($rate->basis=='measure'){
                  $amount=$rate->rate*$detail->measure;
                }  
                if($rate->purpose=='charge'){
                    if (array_key_exists($key, $bl_charges)){            
                      $bl_charges[$key][0]["amount"] +=$amount;
                    }else{          
                      $bl_charges[$key][]= array(
                        "charge_item_id" =>$rate->charge_item_id , 
                        "amount" =>$amount ,
                        "billoflading_id" => $detail->billoflading_id
                      );
                    }
                 }
                 if($rate->purpose=='value'){      
                   //echo   $detail->billoflading_id .' -xxx- '. $amount.' ??  ';           
                  if (array_key_exists($detail->billoflading_id, $bl_values)){            
                    $bl_values[$detail->billoflading_id][0]["amount"] +=$amount;
                   // echo  ' <br>   '.$bl_values[$detail->billoflading_id][0]["amount"].'      #### '.$detail->billoflading_id;
                  }else{          
                    $bl_values[$detail->billoflading_id][]= array(                      
                      "amount" =>$amount ,
                      "billoflading_id" => $detail->billoflading_id
                    );
                    //echo  ' <br>   '.$bl_values[$detail->billoflading_id][0]["amount"].'      $$$$ '.$detail->billoflading_id;
                  }
               }
             
                

            } 
            if(isset($bl_values[$detail->billoflading_id][0]["amount"])){
               if($bl_values[$detail->billoflading_id][0]["amount"] <40){ 
                 $bl_values[$detail->billoflading_id][0]["amount"] =40;
              }
            }
             
          }

          foreach($bl_charges as $x => $x_value) {
        
            $sql="INSERT INTO `bill_of_lading_other_charge` ( `charge_item_id`, `amount`, `prepaid_flag`, `attract_gct`, `currency_id`, `billoflading_id`)
            VALUES (".$bl_charges[$x][0]['charge_item_id'].", ".$bl_charges[$x][0]['amount'].", 'c',0 , 1, ".$bl_charges[$x][0]['billoflading_id'].")";
            $insert_result = mysqli_query($mysqli,$sql);
            if (!$insert_result) {
             // http_response_code(404);
            //  echo($sql.'<br>'.mysqli_error($mysqli));
            }
          } 
          
          foreach($bl_values as $x => $x_value) {
        
            $sql="UPDATE  `bill_of_lading` set value_currency=2,value_of_goods=".number_format($bl_values[$x][0]['amount'],0)." where id=".$bl_values[$x][0]['billoflading_id']." and (value_of_goods is null or value_of_goods=0)";            
            
            $update_result = mysqli_query($mysqli,$sql);
            if (!$update_result) {
              http_response_code(404);
              die($sql.'<br>'.mysqli_error($mysqli));
            }
          } 
            //var_dump ($bl_charges);           
            //echo mysqli_affected_rows($mysqli);
            exit();
            break;      

  }
  
  function retrieveReceiptData(){
      
  }
  function calculateQuanCharge($rate,$quan){
    $cost=0;
    switch ($rate) {
      case 4000:
      case 4500:  
          $cost=$rate*$quan;
          break;
      case 4800:
        if($quan==1){
          $cost=$rate*$quan;
        }else{
          $cost=(4000*$quan)+1000;
        }
      break;
    }
    return $cost; 
  }
  

?>
