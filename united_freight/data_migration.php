<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'uniteddatabase');
   $mysqli = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);  

   set_time_limit(2000);
   $search = array("'", "\n\r","\n","\r");
   $replace = array("\'", ", ", "", ", ");

  /* 
   // voyage ->236 ==> bl_id ->13312
   //COMMODITY
   $csv = array_map('str_getcsv', file('.\data_csv\commodity.csv'));
   $sql = " INSERT INTO `commodity` (`id`, `commodity_code`, `description`) VALUES ";
   for ($i=1;$i<sizeof($csv); $i++){  
        $values = "(".$csv[$i][1].",'".$csv[$i][3]."','".$csv[$i][4]."')";
        $result = mysqli_query($mysqli,$sql.$values);   
        if (!$result) {echo mysqli_error($mysqli)."<br>".$sql.$values."<br>";}
   }
   


      //PACKAGE
      $csv = array_map('str_getcsv', file('.\data_csv\packagetype.csv'));
      $sql = " INSERT INTO `package` (`id`, `package_code`, `description`) VALUES ";
      for ($i=1;$i<sizeof($csv); $i++){  
          if ($i>1) { $sql =$sql. " , ";  } 
           $sql =$sql. "(".$csv[$i][1].",'".$csv[$i][3]."','".$csv[$i][4]."')";
          
      }
      $result = mysqli_query($mysqli,$sql);


      //COUNTRY
      $csv = array_map('str_getcsv', file('.\data_csv\country.csv'));
      $sql = " INSERT INTO `country` (`id`, `country_code`, `country_name`) VALUES ";
      for ($i=1;$i<sizeof($csv); $i++){  
          if ($i>1) { $sql =$sql. " , ";  } 
           $sql =$sql. "(".$csv[$i][0].",'".$csv[$i][3]."','".$csv[$i][4]."')";
          
      }
      $result = mysqli_query($mysqli,$sql);
      
    
      //VESSEL
      $csv = array_map('str_getcsv', file('.\data_csv\vessel.csv'));
      $sql = " INSERT INTO `vessel` (`id`, `vessel_name`, `vessel_code`, `lloyd_number`, `country_id`) VALUES ";
      for ($i=1;$i<sizeof($csv); $i++){  
          if ($i>1) { $sql =$sql. " , ";  } 
            $sql =$sql. "(".$csv[$i][1].",'".$csv[$i][3]."','".$csv[$i][2]."','".$csv[$i][12]."',null)";
          
      }
      $result = mysqli_query($mysqli,$sql);
     

       //CHARGEITEM
       $csv = array_map('str_getcsv', file('.\data_csv\chargeItem.csv'));
       $sql = " INSERT INTO `charge_item` (`id`, `item_code`, `description`, `basis`, `currency_id`, `item_rate`, `commodity_id`, `package_id`, `print_seperate`, `gct`, `system_def`) VALUES ";
       for ($i=1;$i<sizeof($csv); $i++){  
           if ($i>1) { $sql =$sql. " , ";  } 
            $sql =$sql. "(".$csv[$i][1].",'".$csv[$i][2]."','".$csv[$i][3]."','fixed',".$csv[$i][31].",".$csv[$i][10].",null,null,0,1,'n')";
           
       }
       $result = mysqli_query($mysqli,$sql);       
      if (!$result) {http_response_code(404); die($sql.'<br>'.mysqli_error($mysqli)); }
     

      //CUSTOM OFFICE
      $csv = array_map('str_getcsv', file('.\data_csv\customsOffice.csv'));
      $sql = " INSERT INTO `custom_office` (`id`, `code`, `description`)  VALUES ";
      for ($i=1;$i<sizeof($csv); $i++){  
          if ($i>1) { $sql =$sql. " , ";  } 
            $sql =$sql. "(".$csv[$i][0].",'".$csv[$i][1]."','".str_replace($search,$replace,$csv[$i][2]) ."')";
          
      }
      $result = mysqli_query($mysqli,$sql);       
      if (!$result) {http_response_code(404); die($sql.'<br>'.mysqli_error($mysqli)); }
*/   
 /*
  //Voyage   replace ' with ''
  $values='';
  $counter=0;
  $csv = array_map('str_getcsv', file('.\data_csv\shipReport.csv'));
  $sql = " INSERT INTO `voyage` (`id`, `vessel_id`, `voyage_number`, `departure_date`, `arrival_date`, `stripped`, `stripped_date`,`mby_arrival_date`, `mby_vessel_id`)  VALUES ";
  for ($i=1;$i<sizeof($csv); $i++){          
        //if($csv[$i][1]<236) continue;        
        $arrival_date=date_add(date_create("1800-12-28"),date_interval_create_from_date_string($csv[$i][3]." days"));
        $arrival_date=date_format($arrival_date,"Ymd");
        if ($csv[$i][3]==0) $arrival_date=0;
        $strip_date=date_add(date_create("1800-12-28"),date_interval_create_from_date_string($csv[$i][10]." days"));
        $strip_date=date_format($strip_date,"Ymd");
        $departure_date=date_add(date_create("1800-12-28"),date_interval_create_from_date_string($csv[$i][8]." days"));
        $departure_date=date_format($departure_date,"Ymd");
        if ($csv[$i][8]==0) $departure_date=0;
        $mby_arrival_date=date_add(date_create("1800-12-28"),date_interval_create_from_date_string($csv[$i][5]." days"));
        $mby_arrival_date=date_format($mby_arrival_date,"Ymd");
        if ($csv[$i][5]==0) $mby_arrival_date=0;
        if ($csv[$i][10]==0) $strip_date=0;        
        if ($counter>0) { $values =$values. " , ";}
        $values =$values. "(".$csv[$i][1].",".$csv[$i][2].",'".$csv[$i][4]."',".$departure_date.",".$arrival_date.",".$csv[$i][9].",".$strip_date.",".$mby_arrival_date.",".$csv[$i][7].")";
        ++$counter;
        if ($counter >50){
            $result = mysqli_query($mysqli,$sql.$values);       
            if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
            $counter=0;
            $values='';
        }
    }
    if ($values!=''){
       $result = mysqli_query($mysqli,$sql.$values);       
       if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
   }
  
  */
/*

  $csv = array_map('str_getcsv', file('.\data_csv\shipReport.csv')); 
   for ($i=1;$i<sizeof($csv); $i++){  
        //if( $csv[$i][1]<236) continue;        
        $departure_date=date_add(date_create("1800-12-28"),date_interval_create_from_date_string($csv[$i][8]." days"));
        $departure_date=date_format($departure_date,"Ymd");
        if ($csv[$i][8]==0) $departure_date=0;
        if ($departure_date==0) continue;
        $sql="UPDATE voyage SET departure_date=".$departure_date." WHERE id=".$csv[$i][1];
        $result = mysqli_query($mysqli,$sql);   
        if (!$result) {echo mysqli_error($mysqli)."<br>".$sql."<br>";}
   }
 */  

/*
       //PORT
       $csv = array_map('str_getcsv', file('.\data_csv\port.csv'));
       $sql = " INSERT INTO `port` (`id`, `port_code`, `port_name`, `country_id`)  VALUES ";
       for ($i=1;$i<sizeof($csv); $i++){  
           if ($i>1) { $sql =$sql. " , ";  } 
             $sql =$sql. "(".$csv[$i][1].",'".$csv[$i][3]."','".str_replace("'","\'", $csv[$i][4]) ."',null)";
       }
       $result = mysqli_query($mysqli,$sql);       
       if (!$result) {http_response_code(404); die($sql.'<br>'.mysqli_error($mysqli)); }
  */
/*
      //Voyage Container
       $csv = array_map('str_getcsv', file('.\data_csv\voyagecontainer.csv'));
       $sql = "INSERT INTO `voyage_container` (`id`, `voyage_id`, `container_number`, `port_origin`, `seal`)   VALUES ";
       for ($i=1;$i<sizeof($csv); $i++){  
        //if($csv[$i][2]<2224) continue; 
        //if($csv[$i][2]<246 ) continue;
             $values = " (".$csv[$i][0].",'".$csv[$i][2]."','".str_replace("'","\'", $csv[$i][3]) ."',null,'')";
             $result = mysqli_query($mysqli,$sql.$values);       
             if (!$result) {echo $sql.'<br>'.mysqli_error($mysqli); }
       }
       
  */  
  /*
//use tps to export format bolid and parent id starting at 19201
        //bill of lading  replace ' with ''
        $voyarr = array();
        $sql="select id from voyage";
        $result = mysqli_query($mysqli,$sql);
        for ($i=0;$i<mysqli_num_rows($result);$i++) {
        $rec= mysqli_fetch_object($result);	  
        $voyarr[$rec->id] = $rec->id; 
        }

       $file = fopen(".\data_csv\BillOfLading.csv","r") or die("Unable to open file!");
        $counter=0;
        $values='';
        $sql = "INSERT INTO `bill_of_lading` (`id`, `parent_bol`, `bol_total`, `bill_of_lading_number`, `port_of_origin`, `port_of_loading`, `port_of_discharge`, `port_of_delivery`, `currency_id`, `consignee_name`, `consignee_address`, `consignee_phone_num`, `consignee_id`, `shipper_name`, `shipper_address`, `shipper_phone_num`, `notify_name`, `notify_address`, `notify_date`, `notify_phone_num`, `master_bol_id`, `voyage_id`, `order_processed`, `receipt_processed`)   VALUES ";
        while(! feof($file)) {            
           $csv=fgetcsv($file); 
           //if($csv[1]<26866 ) continue;   
           if (!isset($voyarr[trim($csv[3])])) { continue;}        
           if ($csv[1] =='0' || $csv[6]=='Bolnumber') continue;             
           if ($values !='') {$values =$values." , ";}                           
           $notify_date=date_add(date_create("1800-12-28"),date_interval_create_from_date_string($csv[15]." days"));
           $notify_date=date_format($notify_date,"Ymd");
           if ($csv[15]=='0') $notify_date=0;
           $porto=$csv[22]; 
           if ($porto=='0') $porto='null';
           $portl=$csv[23]; 
           if ($portl=='0') $portl='null';
           $portd=$csv[24]; 
           if ($portd=='0') $portd='null';
           $portdd=$csv[25]; 
           if ($portdd=='0') $portdd='null';           

           $values=$values."(".$csv[1].",".$csv[35].",0,'".$csv[6] ."',".$porto .",".$portl .",".$portd .",".$portdd .",".$csv[19] .",'".str_replace($search,$replace,$csv[13]) ."','".str_replace($search,$replace, $csv[14]) ."','',null,'".str_replace($search,$replace,$csv[10]) ."','".str_replace($search,$replace, $csv[11]) ."','','".str_replace($search,$replace,$csv[8]) ."','".str_replace($search,$replace, $csv[9]) ."',".$notify_date.",'',".$csv[2] .",".$csv[3] .",".$csv[28].",".$csv[27] .")";
           ++$counter;
           if ($counter >50){
              $result = mysqli_query($mysqli,$sql.$values);       
              if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
              $counter=0;
              $values='';     
           }
           
        }
        if ($values!=''){
            $result = mysqli_query($mysqli,$sql.$values);       
            if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
        }
        fclose($file);
  */


   /* 
    //Bill Of Lading Container
         $values='';
         $csv = array_map('str_getcsv', file('.\data_csv\billofladingcontainer.csv'));
         $sql = "INSERT INTO `bill_of_lading_container`(`id`, `container_number`, `container_size_type_id`, `billoflading_id`)    VALUES ";
        for ($i=1;$i<sizeof($csv); $i++){  
            if($csv[$i][4]<246 ) continue;        
            if ($csv[$i][2] =='0' || trim($csv[$i][5])=='' ) continue;
            $size_type=$csv[$i][3]; 
            if ($csv[$i][3]==0) $size_type='null';  
             $values = "(".$csv[$i][1].",'".$csv[$i][5]."',".$size_type.",".$csv[$i][2] .")";             
             $result = mysqli_query($mysqli,$sql.$values);               
             echo "<br><br>".$sql.$values." <br> ".mysqli_error($mysqli);
         }
     
             $values='';
    $csv = array_map('str_getcsv', file('.\data_csv\billofladingcontainer.csv'));    
   for ($i=1;$i<sizeof($csv); $i++){  
       if($csv[$i][4]!=246 && $csv[$i][4]!=520 && $csv[$i][4]<236) continue;        
       if ($csv[$i][2] =='0' || trim($csv[$i][5])=='' ) continue;   
       if ($csv[$i][3] ==0) continue;
       $sql = "UPDATE `bill_of_lading_container` SET `container_size_type_id`=".$csv[$i][3]." WHERE `id`=".$csv[$i][1];                 
        $result = mysqli_query($mysqli,$sql);       
    }
    
*/


$blarr = array();
$sql="select id from bill_of_lading";
$result = mysqli_query($mysqli,$sql);
for ($i=0;$i<mysqli_num_rows($result);$i++) {
   $rec= mysqli_fetch_object($result);	  
   $blarr[$rec->id] = $rec->id; 
}

/*
             //Bill of lading Detail  export from topspeed using ^ as the delimitor FIND AND REPLACE "" with " 
             $searchx = array('"',"'", "\n\r","\n","\r");
             $replacex = array('',"", ", ", "", ", ");
             $file = fopen(".\data_csv\BillOfLadingDetail.csv","r") or die("Unable to open file!");
             $values='';
             $counter=0;
             $sql = "INSERT INTO `bill_of_lading_detail`(`id`, `billoflading_id`, `package_type_id`, `commodity_id`, `Description_of_goods`, `number_of_items`, `weight`, `measure`, `weight_unit`, `measure_unit`, `width`, `depth`, `breath`, `volume`)   VALUES ";
             while(! feof($file)) {            
                $csv=fgetcsv($file,2000,"^");
               // echo "<br><br>";
               // var_dump($csv);
                if ($csv[0] =='0' || trim($csv[0])=='' ) continue;  
                //if($csv[2] <13312) continue;  //13312   || $csv[0]< 120368
                //echo (array_key_exists($csv[2], $blarr).'<br> passing this point'.$csv[0]."  ".$csv[2]);
                if (!isset($blarr[trim($csv[2])])) { continue;}    
                
                //if($csv[0] <   147760) continue;  //record number
                $weight_unit='lb';
                if ($csv[9]=='kg')  $weight_unit='kg';  
                $measure_unit='cum';
                if (trim($csv[11])=='cu feet')  $measure_unit='cuf';  
                if ($values !='') {$values =$values." , ";}                           
                 $values =$values."(".$csv[0].",".$csv[2].",". $csv[3] .",". $csv[7] .",'". str_replace($searchx,$replacex,trim($csv[5])) ."',". str_replace(',','',trim($csv[6])).",". $csv[8].",". $csv[10].",'".$weight_unit."','".$measure_unit."',0,0,0,0)"; 
                 ++$counter;
                 if ($counter >50){
                     $result = mysqli_query($mysqli,$sql.$values);       
                     if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
                     $counter=0;
                     $values='';
                 }
             }
             if ($values!=''){
                $result = mysqli_query($mysqli,$sql.$values);       
                if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
            }
            fclose($file);
       
*/
      /*      
            //BillOfLadingOtherCharges
            $file = fopen(".\data_csv\BillOfLadingOtherCharges.csv","r") or die("Unable to open file!");
            $values='';
            $counter=0;
            $sql = "INSERT INTO `bill_of_lading_other_charge`(`id`, `charge_item_id`, `amount`, `prepaid_flag`, `attract_gct`, `currency_id`, `billoflading_id`)   VALUES ";
            while(! feof($file)) {            
               $csv=fgetcsv($file);
               //if($csv[1] <13312) continue; 
               //if($csv[0] < 263582) continue; 
               if($csv[2] ==0) continue;                
               if ($csv[0] =='0' || trim($csv[0])=='' ) continue;  
               
               if (!isset($blarr[trim($csv[1])])) { continue;}     
               if ($values !='') {$values =$values." , ";} 
               $currency=$csv[5];
               if ($currency!=2) $currency=1;
               $values =$values. "(".$csv[0].",".$csv[2].",". $csv[4] .",'c',". $csv[10] .",".$currency.",". $csv[1].")";                 
                 ++$counter;
                 if ($counter >50){
                     $result = mysqli_query($mysqli,$sql.$values);       
                     if (!$result) {
                         if (strpos(mysqli_error($mysqli), 'FOREIGN') && strpos(mysqli_error($mysqli), 'billoflading_id')) {
                            $counter=0;
                            $values='';
                            continue;}
                         http_response_code(404); 
                         die($sql.$values.'<br>'.mysqli_error($mysqli)); 
                     }
                     $counter=0;
                     $values='';
                 }
            }
            if ($values!=''){
                $result = mysqli_query($mysqli,$sql.$values);       
                if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
            }
            fclose($file);
     */    
          
     //USER ID
     $user = array();
     $sql="select * from user_profile";
     $result = mysqli_query($mysqli,$sql);
     for ($i=0;$i<mysqli_num_rows($result);$i++) {
        $rec= mysqli_fetch_object($result);	  
        $user[$rec->user_name] = $rec->id; 
     }
     echo $user["oniel"];
/*
      //ORDER
      $file = fopen(".\data_csv\OrderFile.csv","r") or die("Unable to open file!");
      $values=''; $counter=0;
      $sql = " INSERT INTO `shipment_order`(`id`, `voyage_id`, `billoflading_id`, `created_by`, `locked`, `printed`, `cancelled`, `order_date`, `cancel_by`, `cancel_date`, `cancel_time`)  VALUES ";
      while(! feof($file)) {            
            $csv=fgetcsv($file);
            echo "<br><br>";
            var_dump($csv);
          // if ($csv[2]<236) continue;
           if ($csv[1]==0) continue;
           if (!isset($blarr[trim($csv[3])])) { continue;}    
           $created_by="null"; $cancel_by="null"; $cancel_date="0";
           if ($csv[7]==1){
               $uid=trim(str_replace(" ","",$csv[9]));
               if ($user[$uid]) { $cancel_by=$user[$uid];}
               $cancel_date=$csv[10];
           }
           $uid=str_replace(" ","",$csv[4]);
           if ($user[$uid]) { $created_by=$user[$uid];}
           if ($values !='') {$values =$values." , ";}     
           $values =$values. "(".$csv[1].",".$csv[2].",".$csv[3].",".$created_by.",0,1,".$csv[7].",".$csv[8].",".$cancel_by.",".$cancel_date.",0)";
            ++$counter;
            if ($counter >50){
                $result = mysqli_query($mysqli,$sql.$values);       
                if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
                $counter=0;
                $values='';
            }        
            
       }
        if ($values!=''){
            $result = mysqli_query($mysqli,$sql.$values);       
            if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
        }
        fclose($file);
*/

/*
     //RECEIPT replace all \ in csv file
     $file = fopen(".\data_csv\Receipt.csv","r") or die("Unable to open file!");
      $values='';
      $counter=0;
      $sql = " INSERT INTO `receipt` (`id`, `receipt_date`, `receipt_time`, `client_id`, `payee`, `receipt_total`, `currency_id`, `local_total`, `exchange_rate`, `printed`, `created_by`, `billoflading_id`, `cancel_by`, `cancel_date`, `cancel_time`, `cancelled`, `customer_identification`)  VALUES ";
      while(! feof($file)) {            
            $csv=fgetcsv($file);
           if (trim($csv[19])=='') $csv[19]='0'; 
           if (floatval($csv[1])==0) continue;
           if ($csv[1]=='') continue; 
           //if ($csv[16]<13312) continue;      //that number is the last receipt number
           $created_by="null"; $cancel_by="null"; $cancel_date="0";
           if ($csv[13]==1){
               $uid=str_replace(" ","",$csv[18]);
               if ($user[$uid]) { $cancel_by=$user[$uid];}
               $cancel_date=$csv[10];
           }
           $uid=str_replace(" ","",$csv[15]);
           //if ($user[$uid]) { $created_by=$user[$uid];}
           $created_by=7;
           if ($csv[8]==0) $csv[8]=1;  
           if ($values !='') {$values =$values." , ";}     
             $values =$values. "(".$csv[1].",".$csv[3].",".$csv[4].",null,'".str_replace("'","\'",$csv[6])."',".str_replace( ',', '',$csv[7]).",".$csv[8].",".str_replace( ',', '',$csv[7]).",1,1,".$created_by.",".$csv[16].",".$cancel_by.",".$csv[19].",0,".$csv[13].",'".str_replace("'","\'",$csv[17])."')";
            ++$counter;
            if ($counter >50){
                $result = mysqli_query($mysqli,$sql.$values);       
                if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
                $counter=0;
                $values='';
            }  
            
       }
       if ($values!=''){
          $result = mysqli_query($mysqli,$sql.$values);       
         if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
       }
       fclose($file);
*/
/*
    //RECEIPT DETAIL 
      $file = fopen(".\data_csv\ReceiptDetail.csv","r") or die("Unable to open file!");
      $values=''; $counter=0;
      $sql = " INSERT INTO `receipt_detail`(`id`, `receipt_id`, `bol_id`, `charge_item_id`, `amount`, `currency_amount`, `discount`, `comment`)  VALUES ";
      while(! feof($file)) {            
            $csv=fgetcsv($file);
            //if ($csv[0]<=45098) continue;  
           // if ($csv[0]<=102569) continue;  
            $charge_id=$csv[8];
            if ($charge_id==-15) $charge_id=-1;
            if ($charge_id==0) continue;       
            if ($values !='') {$values =$values." , ";}     
             $values =$values. "(".$csv[0].",".$csv[1].",".$csv[3].",".$charge_id.",".str_replace(",","",$csv[9]).",".str_replace(",","",$csv[9]).",0,'')";
            ++$counter;
            if ($counter >0){
                $result = mysqli_query($mysqli,$sql.$values);       
                if (!$result) {
                    if (strpos(mysqli_error($mysqli), 'FOREIGN') && strpos(mysqli_error($mysqli), 'receipt_id')) {
                        $counter=0;
                        $values='';
                        continue;
                    }

                   http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); 
                  echo ('<br>'.'<br>'.$sql.$values.'<br>'.mysqli_error($mysqli)); 
                }//else {var_dump($csv); break;}
                $counter=0;
                $values='';
            }  
            //if ($counter==100) {break;}
       }
       if ($values!=''){
            $result = mysqli_query($mysqli,$sql.$values);       
            if (!$result) {http_response_code(404); die($sql.$values.'<br>'.mysqli_error($mysqli)); }
        }
        fclose($file);
   */
       echo "completed";
  
 /*
  $profile = array("AMEALIA","andre","ANESIA","brian","chevaughn","Chevelle","christina","CHRISTOPHER","deniel","dennis","DEVAINE","DWAYNE","fabian","GILPIN","HUNTLEY","JASON","Javeine","JELESIA","jerdene","JESSICA","JHEANELLE","KAY-ANN","KAYMARIE","Kelia","KEVEISHA","KOMONE","MARK","MARLINE","monica","Moniesa","myriam","MYRON","NATALIE","nicola","NORDIA","Omari","ONIEL","PHILLIP","RANDOLPH","Reccreatedby","ROBERT","RODNEY","RYAN","Shamay","SHANDANE","SHANEEN","SHARIFA","Shashie Gaye","SHERICA","SIDONNIE","TOWANA","Tresha","USER","VENESA","VENNESA");
  for ($i=0;$i<sizeof($profile); $i++){
    $sql="INSERT INTO `user_profile`(`user_name`, `password`, `full_name`, `status`) ";
    $sql=$sql." VALUES ('".str_replace(" ","",$profile[$i])."','".str_replace(" ","",$profile[$i])."','".$profile[$i]."','A')";
    $result = mysqli_query($mysqli,$sql);   
    if (!$result) { http_response_code(404); die($sql.'<br>'.mysqli_error($mysqli)); }
  }
  */


?>