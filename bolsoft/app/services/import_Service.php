
<?php
global $baseURL;
if($this->requestUrl[1]=='miami'){
     $baseURL="http://";
}
if($this->requestUrl[1]=='newyork'){
     $baseURL="http://ebolsoft.qualityoneintlshipping.com";
}
switch ($this->requestUrl[2]) {
    case 'booking':    
    getBookingData($this->requestUrl[3]);
    exit();
    break;
    case 'manifest':    
      importManifest($this->requestUrl[3],$this->post_data,$this->db->conn);
      exit();
      break;
    case 'update_strip':    
        updateStrip($this->post_data,$this->db->conn);
        exit();
        break;  
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }
  
  function updateStrip($post_data,$mysqli){
    global $baseURL;
//  var_dump($post_data);
  
    $user =array("user_name" => "kingstonuser","password" => base64_encode("qualityone"));                
    $result =CallAPI('POST',$baseURL.'/app/login',$user,"");
    $token = json_decode($result, true);

    $obj =array("stripped" => $post_data["stripped"],"stripped_date" => $post_data["stripped_date"]);                

    $data =CallAPI('PUT',$baseURL.'/app/booking/'.$post_data["booking_id"],$obj,$token["token"]);
    var_dump($data);

  }
  function importManifest($booking_id,$post_data,$mysqli){
    global $baseURL;
    $sql="select id,description from commodity ";
    $commodity_list = mysqli_query($mysqli,$sql);	
    $sql="select id,description from package ";
    $package_list = mysqli_query($mysqli,$sql);	
    $translation_source_id=getTranslationSourceId($mysqli);

    $user =array("user_name" => "kingstonuser","password" => base64_encode("qualityone"));                
    $result =CallAPI('POST',$baseURL.'/app/login',$user,"");
    $token = json_decode($result, true);
    //$data =CallAPI('GET','http://ebolsoft.qualityoneintlshipping.com/app/export/master/'.$booking_id,$user,$token["token"]);
    $master=getMaster($booking_id,$token["token"]);    
    mysqli_autocommit($mysqli,FALSE);
//1) Create Voyage
    $sql="INSERT INTO `voyage` ( booking_id,`vessel_id`, `voyage_number`, `departure_date`, `arrival_date`, `stripped`, `stripped_date`, `transportation_mode`, `manifest_number`, `registration_number`)";
    $sql =$sql." VALUES ( ".$booking_id.",".$post_data["vessel_id"].", '".$post_data["voyage_number"]."', ".$post_data["departure_date"].", ".$post_data["arrival_date"].", 0, 0, 'S', '".$post_data["manifest_number"]."', '".$post_data["registration_number"]."')";
    $result = mysqli_query($mysqli,$sql);
    if (!$result) {  handleRollBack($mysqli,$sql);   }
    $voyage_id=mysqli_insert_id($mysqli);
   // echo "<br>".$sql;   

//2) Create Master BL
$sql="INSERT INTO `bill_of_lading` ( `parent_bol`, `bill_of_lading_number`, `port_of_origin`, `port_of_loading`, `port_of_discharge`, `port_of_delivery`, `currency_id`, `consignee_name`, `consignee_address`, `shipper_name`, `shipper_address`, `master_bol_id`, `voyage_id`, `order_processed`, `receipt_processed`, `value_of_goods`, `value_currency`, `customer_type`, `status`) ";
$sql=$sql." VALUES ( 1,'SMLU".$master[0]["bill_of_lading_number"]."', ".$post_data["port_of_origin"].", ".$post_data["port_of_origin"].",".$post_data["port_of_discharge"].", ".$post_data["port_of_discharge"].", 1, '".mysql_real_escape_string($master[0]["consignee_fname"])."', '".mysql_real_escape_string($master[0]["consignee_address"])."', '".mysql_real_escape_string($master[0]["shipper_fname"])."', '".mysql_real_escape_string($master[0]["shipper_address"])."',0,".$voyage_id.",0,0,0,2,'', 'release') ";
$result = mysqli_query($mysqli,$sql);
if (!$result) {  handleRollBack($mysqli,$sql);   }
$master_id=mysqli_insert_id($mysqli);
//echo "<br>".$sql;

//3) Add Container to master
$sql="INSERT INTO `bill_of_lading_container` ( `container_number`, `container_size_type_id`, `billoflading_id`) ";
$sql=$sql." VALUES ( '".$master[0]["container_number"]."', ".$post_data["container_size_type_id"].", ".$master_id.")";
$result = mysqli_query($mysqli,$sql);
if (!$result) {  handleRollBack($mysqli,$sql);   }
//echo "<br>".$sql;

//4) Create House Bls
$house=getHouse($booking_id,$token["token"]);
$sql="INSERT INTO `bill_of_lading` ( `parent_bol`, `bol_total`, `bill_of_lading_number`, `port_of_origin`, `port_of_loading`, `port_of_discharge`, `port_of_delivery`, `currency_id`, `consignee_name`, `consignee_address`, `consignee_phone_num`, `shipper_name`, `shipper_address`, `shipper_phone_num`, `notify_name`, `notify_address`, `notify_date`, `notify_phone_num`, `master_bol_id`, `voyage_id`, `order_processed`, `receipt_processed`, `value_of_goods`, `value_currency`, `customer_type`, `status`) VALUES ";
for ($i = 0; $i < count($house); $i++)  {
  if($i>0){$sql=$sql.',';}
  $cname=trim($house[$i]["consignee_fname"].' '.$house[$i]["consignee_sname"].' '.$house[$i]["consignee_other_name"]);
  $sname=trim($house[$i]["shipper_fname"].' '.$house[$i]["shipper_sname"].' '.$house[$i]["shipper_other_name"]);
  $nname=trim($house[$i]["notify_fname"].' '.$house[$i]["notify_sname"].' '.$house[$i]["notify_other_name"]);
  $sql=$sql."  ( 0,0,'".$house[$i]["bill_of_lading_number"]."', ".$post_data["port_of_origin"].", ".$post_data["port_of_origin"].",".$post_data["port_of_discharge"].", ".$post_data["port_of_discharge"].", 1, '".mysql_real_escape_string($cname)."', '".mysql_real_escape_string($house[$i]["consignee_address"])."', '".$house[$i]["consignee_phone_num"]."', '".mysql_real_escape_string($sname)."', '".mysql_real_escape_string($house[$i]["shipper_address"])."', '".$house[$i]["shipper_phone_num"]."','".mysql_real_escape_string($nname)."', '".mysql_real_escape_string($house[$i]["notify_address"])."',0, '".$house[$i]["notify_phone_num"]."',".$master_id.",".$voyage_id.",0,0,0,0,'".$house[$i]["customer_type"]."', 'release') ";
}
$result = mysqli_query($mysqli,$sql);
if (!$result) {  handleRollBack($mysqli,$sql);   }

//mysqli_commit($mysqli);   
//echo "<br>".$sql;
//5) Create Bl Details
$sql="select id,bill_of_lading_number from `bill_of_lading` where master_bol_id=".$master_id;
$data = mysqli_query($mysqli,$sql);	

$bl_ids=array();
foreach($data as $current_rec){ 
    $bl_ids[$current_rec["bill_of_lading_number"]]=$current_rec["id"];
}
$translation_codes=getAllTranslationCodes($mysqli,$translation_source_id);

$goods=getGoods($booking_id,$token["token"]);
$sql="INSERT INTO `bill_of_lading_detail` ( `billoflading_id`, `package_type_id`, `commodity_id`, `Description_of_goods`, `number_of_items`, `weight`, `measure`, `weight_unit`, `measure_unit`) VALUES ";
for ($i = 0; $i < count($goods); $i++)  {
  if($i>0){$sql=$sql.',';}
  if(!isset($translation_codes['commodity:'.$goods[$i]['commodity_id']])){
    $translation_codes=addTranslationCodes($mysqli,'commodity',$goods[$i]['commodity_id'],$goods[$i]['commodity_description'],$commodity_list,$translation_source_id,$translation_codes);
  }  
  if(!isset($translation_codes['package:'.$goods[$i]['package_type_id']])){
    $translation_codes=addTranslationCodes($mysqli,'package',$goods[$i]['package_type_id'],$goods[$i]['package_description'],$package_list,$translation_source_id,$translation_codes);
  }  
  $sql=$sql." ( ".$bl_ids[$goods[$i]["bill_of_lading_number"]].",".$translation_codes['package:'.$goods[$i]['package_type_id']].",".$translation_codes['commodity:'.$goods[$i]['commodity_id']].",'".$goods[$i]['Description_of_goods']."', ".$goods[$i]['number_of_items'].", ".$goods[$i]['weight'].", ".$goods[$i]['measure'].", '".$goods[$i]['weight_unit']."', '".$goods[$i]['measure_unit']."')";
  
}  
$result = mysqli_query($mysqli,$sql);
if (!$result) {  handleRollBack($mysqli,$sql);   }
//6) Add Charges - Freight
$sql="SELECT data_value from system_values where code='freight_id'";
$result = mysqli_query($mysqli,$sql);
$freight= mysqli_fetch_object($result);	
$charges=getCharges($booking_id,$token["token"]);
if(count($charges)>0){
    $sql="INSERT INTO `bill_of_lading_other_charge` ( `charge_item_id`, `amount`, `prepaid_flag`, `attract_gct`, `currency_id`, `billoflading_id`) VALUES ";
    for ($i = 0; $i < count($charges); $i++)  {
      if($i>0){$sql=$sql.',';}
      $sql=$sql." (".$freight->data_value.", ".$charges[$i]["balance"].",'c',0,2,".$bl_ids[$charges[$i]["bill_of_lading_number"]].") ";
    }
    $result = mysqli_query($mysqli,$sql);
    if (!$result) {  handleRollBack($mysqli,$sql);   }
}

mysqli_commit($mysqli);   
echo (json_encode($house));
   /* $vessel = array();
  
    $booking=json_decode($data, true);
    for ($x = 0; $x < sizeof($booking) ; $x++) {
      if(!isset($vessel[$booking[$x]["vessel_id"]])) {
        $vessel[$booking[$x]["vessel_id"]] =CallAPI('GET','http://ebolsoft.qualityoneintlshipping.com/app/vessel/'.$booking[$x]["vessel_id"],$user,$json["token"]);
      }  
        $booking[$x]["vessel_id"]=json_decode($vessel[$booking[$x]["vessel_id"]]);
        
    }    
  
    echo json_encode($booking);
    */
  }
  
function getCharges($booking_id,$token){
  global $baseURL;
  $data =CallAPI('GET',$baseURL.'/app/export/billoflading_charge/'.$booking_id,"",$token);
  return json_decode($data, true);
} 
function getGoods($booking_id,$token){
  global $baseURL;
  $data =CallAPI('GET',$baseURL.'/app/export/billoflading_detail/'.$booking_id,"",$token);
  return json_decode($data, true);
}  
function getMaster($booking_id,$token){
  global $baseURL;
  $data =CallAPI('GET',$baseURL.'/app/export/master/'.$booking_id,"",$token);
  return json_decode($data, true);
}
function getHouse($booking_id,$token){
  global $baseURL;
  $data =CallAPI('GET',$baseURL.'/app/export/billoflading/'.$booking_id,"",$token);
  return json_decode($data, true);
}
  function getBookingData($port){
    global $baseURL;
  
  $user =array("user_name" => "kingstonuser","password" => base64_encode("qualityone"));      
  //$user =array("user_name" => "mobayuser","password" => base64_encode("qualityone"));    
  
  $token ="";
  $result =CallAPI('POST',$baseURL.'/app/login',$user,$token);
  $json = json_decode($result, true);
  $data =CallAPI('GET',$baseURL.'/app/export/booking/'.$port,$user,$json["token"]);
  $vessel = array();

  $booking=json_decode($data, true);
  for ($x = 0; $x < sizeof($booking) ; $x++) {
    if(!isset($vessel[$booking[$x]["vessel_id"]])) {
      $vessel[$booking[$x]["vessel_id"]] =CallAPI('GET',$baseURL.'/app/vessel/'.$booking[$x]["vessel_id"],$user,$json["token"]);
    }  
      $booking[$x]["vessel_id"]=json_decode($vessel[$booking[$x]["vessel_id"]]);
      
  }    

  echo json_encode($booking);
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
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen(json_encode($data))));
          curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);        
          curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
         break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

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
function addTranslationCodes($mysqli,$type,$external_id,$desc,$list,$hid,$codes){
  $perc=0; $id=0; $p=0;
  foreach($list as $l){
     similar_text(strtoupper($desc), strtoupper($l["description"]), $p);
    // echo "<br>".$type.":: ".$p.": Percentage : ".$perc." Description -> ".$l["description"]." : ".$desc;
     if(strstr( strtoupper($l["description"]), strtoupper($desc)) && $desc!="" && $p<74){$p=74;}
     if($p>$perc){$perc=$p; $id=$l["id"]; }
     if($perc>75) {break;}
  }
  
  $sql="INSERT INTO `edi_translation` ( `type`, `internal_code`, `external_code`, `code_id`, `translation_source_id`) ";
  $sql=$sql." VALUES ('".$type."', NULL,'".$external_id."',".$id.",".$hid.")";
  $result = mysqli_query($mysqli,$sql);
  if (!$result) {  handleRollBack($mysqli,$sql);   }
  $codes[$type.':'.$external_id]=$id;
  return $codes;
}  

function getTranslationSourceId($mysqli){
  $head_id=0;
  $sql="SELECT * FROM `translation_source` where code='direct_import'";
  $result = mysqli_query($mysqli,$sql);
  if (mysqli_num_rows( $result) ==0){
   $sql="INSERT INTO `translation_source` ( `code`, `description`) VALUES ('direct_import','Direct Import')";
   $result = mysqli_query($mysqli,$sql);
   $head_id=mysqli_insert_id($mysqli);
  }else{
   $head= mysqli_fetch_object($result);	
   $head_id=$head->id;
  }  
  return $head_id;
}

function getAllTranslationCodes($mysqli,$head_id){
  $codes=array();
   $sql="SELECT * FROM `edi_translation` where translation_source_id=".$head_id;
   $result = mysqli_query($mysqli,$sql);
   foreach($result as $rec){ 
     $codes[$rec["type"].':'.$rec["external_code"]]=$rec["code_id"];
   }  
   return $codes;
}
/*
$sim = similar_text('bafoobar', 'barfoo', $perc);
echo "similarity: $sim ($perc %)\n";
$sim = similar_text('barfoo', 'bafoobar', $perc);
echo "similarity: $sim ($perc %)\n";

The above example will output something similar to:

similarity: 5 (71.428571428571 %)
similarity: 3 (42.857142857143 %)


*/

?>
