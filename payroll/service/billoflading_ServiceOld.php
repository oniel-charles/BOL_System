<?php
switch ($service_type) {
  case 'charges':         
  date_default_timezone_set('America/Jamaica');
  $cur_date= date('Ymd') ;
  
  // get bl info
   $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".intval($bl_id);
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

  $user_name=$claims['full_name'];
  $gct_total=0;
  $receipt_total=0;
  $currency_total=0;
  $service_result = array();
  // GET DETAIL EITHER FROM RECEIPT OR FROM BILL OF LATING FOR RECEIPTS 
  $freight_str='';
  if ($request[2]=='freight'){
    $freight_str =' and b.charge_item_id ='.intval($request[3]);
  }
  if ($request[2]=='no_freight'){
    $freight_str =' and b.charge_item_id <>'.intval($request[3]);
  }
  $sql="SELECT d.charge_item_id,u.user_name,u.full_name,r.id,r.receipt_date,r.receipt_time,c.description,d.amount,d.amount as paid FROM (((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where (r.cancelled=0 or r.cancelled is null) and r.billoflading_id=".intval($bl_id);  
  $charges_paid = mysqli_query($mysqli,$sql);
  if (!$charges_paid) {
    http_response_code(404);
    die($sql.'<br>'.mysqli_error($mysqli));
  }
  //$sql="SELECT b.attract_gct,c.currency_id as item_currency, b.currency_id as bill_currency ,b.prepaid_flag,b.charge_item_id,'' as user_name, '' as full_name,0 id, 0 as receipt_date, 0 as receipt_time,c.description,b.amount,0 as paid FROM (((`bill_of_lading_other_charge` as b left join receipt_detail as d on b.charge_item_id=d.charge_item_id and b.billoflading_id=d.bol_id) left join receipt as r on d.receipt_id=r.id ) left join charge_item as c on b.charge_item_id=c.id) where d.id is null and (r.cancelled=0 or r.cancelled is null) ".$freight_str." and b.billoflading_id =".intval($bl_id);  
  $sql="SELECT  b.attract_gct,c.currency_id as item_currency, b.currency_id as bill_currency ,b.prepaid_flag,b.charge_item_id,'' as user_name, '' as full_name,0 id, 0 as receipt_date, 0 as receipt_time,c.description,b.amount,0 as paid FROM ((`bill_of_lading_other_charge` as b left join (select dt.id,dt.charge_item_id,dt.bol_id from receipt as hd left join receipt_detail as dt on hd.id=dt.receipt_id where (hd.cancelled=0 or hd.cancelled is null) and hd.billoflading_id=".intval($bl_id)." ) as r on b.charge_item_id=r.charge_item_id and b.billoflading_id=r.bol_id ) left join charge_item as c on b.charge_item_id=c.id) where r.id is null   ".$freight_str." and b.billoflading_id =".intval($bl_id); 
  $charges_unpaid = mysqli_query($mysqli,$sql); 
  //echo $sql; 
  if (!$charges_unpaid) {
    http_response_code(404);
    die($sql.'<br>'.mysqli_error($mysqli));
  }

  
       $new_receipt=true;
      //$sql="SELECT b.charge_item_id,b.prepaid_flag,i.description,b.amount, c.currency_code,b.attract_gct,i.currency_id as item_currency, b.currency_id as bill_currency FROM ((`bill_of_lading_other_charge` as b left join currency as c on b.currency_id=c.id) left join charge_item as i on b.charge_item_id=i.id) WHERE b.billoflading_id=".intval($bl_id);
      $result = mysqli_query($mysqli,$sql);
         // echo '<br>unpaid ='.mysqli_num_rows($charges_unpaid).'<br>';
      $roe="";    
      for ($i=0;$i<mysqli_num_rows($charges_unpaid);$i++) {
          $rec= mysqli_fetch_object($charges_unpaid);	 
          if ($rec->prepaid_flag=='c'){    
            $desc=$rec->description;            
            $currency_amount=$rec->amount;
            if ($rec->item_currency==2 && $rec->bill_currency==1){        
              $roe=" R.O.E @".$us_rate->exchange_rate;
              $rec->amount = round($rec->amount * $us_rate->exchange_rate,2);              
            } 
            //if ( $rec->bill_currency==1){ $roe=" R.O.E @".$us_rate->exchange_rate;}              
            $currency_total+=$currency_amount;
            $receipt_total +=$rec->amount;    
            if ($rec->attract_gct=="1") {$gct_total+=$rec->amount;} 
              $service_result[] = array("roe"=>$us_rate->exchange_rate,"currency_amount"=>$currency_amount,"receipt_number"=>0,"date"=>0,"item"=>$rec->charge_item_id,"bill_currency"=>$rec->bill_currency,"item_currency"=>$rec->item_currency, "gct"=> $rec->attract_gct,"description"=> $desc, "amount" => $rec->amount);
           /* if ($rec->bill_currency==1 && $rec->item_currency==2){          
              $service_result[] = array("currency_amount"=>0,"paid"=>0,"receipt_number"=>0,"date"=>0,"item"=>-2,"bill_currency"=>1, "gct"=>0,"description"=> "Sur Charge @".$sur_rec->item_rate." %", "amount" => round(($sur_rec->item_rate * .01 * $rec->amount),2));
              $receipt_total +=round(($sur_rec->item_rate * .01 * $rec->amount),2);
            } */
            $receipt_currencey=$rec->bill_currency;
        }
      }
      if ($gct_total>0){
        $gct_total = round($gct_total *.01 * $gct_rec->rate,2);
        $service_result[] = array("currency_amount"=>0,"receipt_number"=>0,"date"=>0,"item"=>-1,"bill_currency"=>1, "gct"=>0,"description"=> "GCT", "amount" => $gct_total);
        $receipt_total +=$gct_total;
      }
      if ( $request[1]!='create'){
        for ($i=0;$i<mysqli_num_rows($charges_paid);$i++) {         
          $rec= mysqli_fetch_object($charges_paid);	                     
          $service_result[] = array("currency_amount"=>0,"paid"=>$rec->paid,"item"=>$rec->charge_item_id,"receipt_number"=>$rec->id,"date"=>$rec->receipt_date,"time"=>$rec->receipt_time,"bill_currency"=>0, "gct"=>0,"description"=> $rec->description, "amount" => $rec->amount);      
        }
      }
    
        
    break;
    case 'reprint':         
    $bl_id=0;
    $sql="SELECT c.currency_id as charge_currency,r.exchange_rate,r.currency_id,r.billoflading_id,d.currency_amount,d.charge_item_id,u.user_name,u.full_name,r.id,r.receipt_date,r.receipt_time,c.description,d.amount,d.amount as paid FROM (((`receipt` as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where (r.cancelled=0 or r.cancelled is null) and r.id=".intval($rec_id);
    $result = mysqli_query($mysqli,$sql);
    for ($i=0;$i<mysqli_num_rows($result);$i++) {           
         $rec= mysqli_fetch_object($result);	
         $bl_id=$rec->billoflading_id;                  
         $receipt_currencey=$rec->currency_id;
         $user_name=$rec->full_name;         
         if($rec->charge_currency==2){
           $roe='R.O.E @'.$rec->exchange_rate;
           $currency_code='USD';
         }
         $service_result[] = array("currency_amount"=>$rec->currency_amount,"item"=>$rec->charge_item_id,"receipt_number"=>$rec->id,"date"=>$rec->receipt_date,"time"=>$rec->receipt_time,"bill_currency"=>0, "gct"=>0,"description"=> $rec->description, "amount" => $rec->amount);      
    }
      
  // get bl info
   $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".intval($bl_id);
   $result = mysqli_query($mysqli,$sql);
   $bill_rec= mysqli_fetch_object($result);	

    break;
    case 'select':         
    $sql="SELECT b.*,currency_code FROM `bill_of_lading_other_charge` as b left join currency on b.currency_id=currency.id WHERE b.billoflading_id=".preg_replace('/[^a-z0-9_]+/i','',$request[3]);
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
      $charge_id=preg_replace('/[^a-z0-9_]+/i','',$request[3]);  
      $voyage_id=preg_replace('/[^a-z0-9_]+/i','',$request[2]);
      $sql="SELECT * from charge_item where id=".$charge_id."  limit 1 " ;
      $result = mysqli_query($mysqli,$sql);
      $item_rec= mysqli_fetch_object($result);	   

      $sql="SELECT id FROM bill_of_lading where id not in (SELECT b.id FROM bill_of_lading  as b left join `bill_of_lading_other_charge` as c on b.id=c.billoflading_id WHERE b.voyage_id=".$voyage_id." and c.charge_item_id=".$charge_id.") and voyage_id=".$voyage_id." and (parent_bol is null or parent_bol=0) and (receipt_processed is null or receipt_processed=0)" ;
      $result = mysqli_query($mysqli,$sql);
     // echo "number of rows".mysqli_num_rows($result)."\n";
      for ($i=0;$i<mysqli_num_rows($result);$i++) {
           $rec= mysqli_fetch_object($result);	
           $sql = " INSERT INTO `bill_of_lading_other_charge` ( `charge_item_id`, `amount`, `prepaid_flag`, `attract_gct`, `currency_id`, `billoflading_id`) ";
           $sql =$sql. "  VALUES (".$charge_id.",".$item_rec->item_rate.",'c',".$item_rec->gct.",".$item_rec->currency_id.",".$rec->id.")";         

           $upd_result = mysqli_query($mysqli,$sql);
           if (!$upd_result) {
             http_response_code(404);
             die($sql.'<br>'.mysqli_error($mysqli));
           }
      }  
      echo mysqli_num_rows($result);
      break;
    case 'unapply_charge':           
      $charge_id=preg_replace('/[^a-z0-9_]+/i','',$request[3]);  
      $voyage_id=preg_replace('/[^a-z0-9_]+/i','',$request[2]);
      $sql=" delete from  bill_of_lading_other_charge where charge_item_id=".$charge_id." and  billoflading_id in (SELECT b.id FROM bill_of_lading as b left join (select * from `bill_of_lading_other_charge`) as c on b.id=c.billoflading_id WHERE b.voyage_id=$voyage_id and c.charge_item_id=$charge_id and (b.receipt_processed is null or b.receipt_processed=0))";
      $result = mysqli_query($mysqli,$sql);        
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }
      echo mysqli_affected_rows($mysqli);
      break;    
      case 'admin_update_assign':         
      $ids=implode(",",$input['ids']);

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
      $ids=implode(",",$input['ids']);

      $sql="update bill_of_lading set receipt_processed=0 where receipt_processed=2 and id in (".$ids.") ";
      $upd_result = mysqli_query($mysqli,$sql);
      if (!$upd_result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }else{
         echo mysqli_affected_rows($mysqli);
      }
      break;

  }

  function retrieveReceiptData(){
      
  }
  

?>
