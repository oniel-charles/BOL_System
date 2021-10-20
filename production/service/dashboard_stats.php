<?php
require_once '../user_access.php';
require_once '../numbersToWords.php';


date_default_timezone_set('America/Jamaica');
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$post_data = json_decode(file_get_contents('php://input'),true);
//echo $post_data->cus_id;
//echo $post_data->no_order;

//exit();
// connect to the mysql database
if (mysqli_connect_errno()){
  header("HTTP/1.1 401 Unauthorized");
  exit(mysqli_connect_error());
}
mysqli_set_charset($mysqli,'utf8'); 

$claims=authenticateToken();
if ($claims==null){    
    //     header("HTTP/1.1 401 Unauthorized");
     //     exit('invalid token');
        
  }
  
  //Get Company 
  $sql="SELECT  * from company limit 1";
 $company = mysqli_query($mysqli,$sql);
 $company_rec= mysqli_fetch_object($company);	
 //$company_rec->company_address

 $date = new DateTime('NOW');  
$dt=$date->format('Y-m-d');
$start_date=date("Ym01", strtotime($dt));
$end_date=date("Ymt", strtotime($dt));
//echo 'First day : '.$start_date.' - Last day : '. $end_date; 

$stats_data = array();
$cashier_arr = array();
$charges_arr = array();
$charges_arr_cur = array();
$charges_arr_prv = array();

//FOR CURRENT MONTH
//NUMBER OF CUSTOMERS
$sql="SELECT count(b.id) as cnt FROM voyage as v left join `bill_of_lading` as b on v.id=b.voyage_id WHERE parent_bol<>1 and v.arrival_date between $start_date and $end_date ";
$result = mysqli_query($mysqli,$sql);
$customer_cnt= mysqli_fetch_object($result);	

//get freight id
$sql="SELECT data_value from system_values where code='freight_id'";
$result = mysqli_query($mysqli,$sql);
$freight= mysqli_fetch_object($result);	

//get freight amount
$sql="SELECT sum(amount) as amt FROM receipt as r left join receipt_detail as d on r.id=d.receipt_id where d.charge_item_id=$freight->data_value and r.receipt_date between $start_date and $end_date ";
$result = mysqli_query($mysqli,$sql);
$frt= mysqli_fetch_object($result);	

//get Stripped
$sql="SELECT count(c.container_number) as cnt FROM voyage as v left join voyage_container as c on v.id=c.voyage_id where v.stripped<>1 and arrival_date  between $start_date and $end_date ";
$result = mysqli_query($mysqli,$sql);
$not_stripped= mysqli_fetch_object($result);	
if (!$result) {
  http_response_code(500);
  die($sql.'<br>'.mysqli_error($mysqli));
}
$sql="SELECT count(c.container_number) as cnt FROM voyage as v left join voyage_container as c on v.id=c.voyage_id where v.stripped=1 and arrival_date between $start_date and $end_date ";
$result = mysqli_query($mysqli,$sql);
if (!$result) {
  http_response_code(500);
  die($sql.'<br>'.mysqli_error($mysqli));
}
$stripped= mysqli_fetch_object($result);	

//cashier recipt cnt
$sql="SELECT count(r.id) as cnt,u.user_name FROM receipt as r left join user_profile as u on r.created_by=u.id where (r.cancelled=0 or r.cancelled is null) and r.receipt_date between $start_date and $end_date  group by u.user_name";
$cashier = mysqli_query($mysqli,$sql);
if (!$cashier) {
  http_response_code(500);
  die($sql.'<br>'.mysqli_error($mysqli));
}
for ($i=0;$i<mysqli_num_rows($cashier);$i++) {
   $rec= mysqli_fetch_object($cashier);	 
   array_push($cashier_arr, array("cnt"=>$rec->cnt,"user"=>$rec->user_name));
}
  
//Charges 
$sql="SELECT sum(amount) as amt,c.description FROM ((receipt as r left join receipt_detail as d on r.id=d.receipt_id) left join charge_item as c on d.charge_item_id=c.id) where (r.cancelled=0 or r.cancelled is null) and r.receipt_date between  $start_date and $end_date  group by c.description";
$charges = mysqli_query($mysqli,$sql);
if (!$charges) {
  http_response_code(500);
  die($sql.'<br>'.mysqli_error($charges));
}
for ($i=0;$i<mysqli_num_rows($charges);$i++) {
  $rec= mysqli_fetch_object($charges);	 
  array_push($charges_arr, array("amount"=>$rec->amt,"item"=>$rec->description));
}

$c_start_date= Date("Ymd", strtotime( $start_date." -6 Month "));
//Charges per month Current
$sql="SELECT FLOOR(receipt_date/100 mod 1000000) as mth, sum(receipt_total) as total FROM `receipt` WHERE receipt_date between $c_start_date and $end_date group by FLOOR(receipt_date/100 mod 1000000)";
$c_charges = mysqli_query($mysqli,$sql);
if (!$c_charges) {
 http_response_code(500);
 die($sql.'<br>'.mysqli_error($c_charges));
}
for ($i=0;$i<mysqli_num_rows($c_charges);$i++) {
 $rec= mysqli_fetch_object($c_charges);	 
 array_push($charges_arr_cur, array("mth"=>$rec->mth,"total"=>$rec->total));
}


 $p_start_date= Date("Ymd", strtotime( $c_start_date." -1 Year "));
 $p_end_date  = Date("Ymd", strtotime( $end_date." -1 Year "));
//Charges Previous per month
$sql="SELECT FLOOR(receipt_date/100 mod 1000000), sum(receipt_total) as total FROM `receipt` WHERE receipt_date between $p_start_date and $p_start_date group by FLOOR(receipt_date/100 mod 1000000)";
$p_charges = mysqli_query($mysqli,$sql);
if (!$p_charges) {
  http_response_code(500);
  die($sql.'<br>'.mysqli_error($p_charges));
}
for ($i=0;$i<mysqli_num_rows($p_charges);$i++) {
  $rec= mysqli_fetch_object($p_charges);	 
  array_push($charges_arr_prv, array("mth"=>$rec->mth,"total"=>$rec->total));
}

$stats_data[] = array("customer_cnt"=>$customer_cnt->cnt,"freight"=>$frt->amt,"stripped"=>$stripped->cnt,"not_stripped"=>$not_stripped->cnt,"cashier_receipts"=>$cashier_arr,"charges"=>$charges_arr,"cur_charges"=>$charges_arr_cur,"prv_charges"=>$charges_arr_prv);
//echo mysqli_error($mysqli);
//var_dump ($stats_data);
echo json_encode($stats_data);

   $order_number=0;
   $user=$claims['full_name'];

   $order_by='';
   switch ($request[2]) {
    case 'byuser':    
    $order_by=" order by u.user_name  ";
    break;
    case 'byday':    
    $order_by=" order by r.receipt_date  ";
    break;
  }   
  $filter_str='';
  if (intval($request[3])!=0 && $request[2]=='byuser'){
    $filter_str=' and r.created_by='.$request[3];
  }
 $sql="select r.exchange_rate,d.currency_amount,u.user_name,r.payee,r.id,r.receipt_date,d.amount,c.description from (((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where  (r.cancelled is null or r.cancelled=0) and r.receipt_date between $request[0] and $request[1] $filter_str $order_by  ";
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$s_date= new DateTime($request[0]) ;
$e_date= new DateTime($request[1]) ;
$date_range =$s_date->format('d/m/Y').' to '.$e_date->format('d/m/Y');




?>
