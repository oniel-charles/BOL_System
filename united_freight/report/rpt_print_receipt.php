<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';

class PDF extends FPDF
{
function Header()
{
  global $company_rec;
  global $bill_rec;
  global $date;
  global $receipt_number;
  global $roe_str;
  global $currency_code;
  global $reprint;

  $this->SetFont('Arial','B');
$this->Cell( 30, 5,'Date ', 0,'L');
$this->Cell( 20, 5,'', 0,'L');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$this->SetFont('Arial','B',15);
$this->Image('../img/company-icon.jpg',35,10,-200);
$this->Cell(80,10,'United Freight Forwarders LTD',0,0);
$this->SetTextColor(0,0,255);
$this->SetFont('Arial','B',12);
$this->Cell(30,5, $currency_code,0,1,'C');
$this->SetTextColor(0,0,0);
$this->SetFont('Arial','',10);
$this->Cell( 30, 5,$date->format('M d, Y'), 0,'L');
$this->Cell( 20, 5,'', 0,'C');
$this->Cell( 60, 10,'128 Third Street, Newport West, Kgn 13 ', 0,'C');
$this->SetFont('Arial','B',12);
$this->Cell(70,5,'RECEIPT',0,1,'C');
$this->SetFont('Arial','',10);
$this->Cell( 60, 5,$date->format('h:i a'), 0,'L');
$x=$this->GetX()+30;
$y=$this->GetY();	
$this->MultiCell( 60, 5, $company_rec->company_address, 0,'L');
$next_y=$this->GetY();
$this->SetY($y);
$this->SetX($x-35);
$this->Cell( 55, 10,'(876) 757-7138', 0);
$this->SetTextColor(255,0,0);
$this->Cell(70,5,sprintf('%08d',$receipt_number),0,1,'C');
$this->Cell( 155, 5,$reprint, 0,0,'R');
$this->SetTextColor(0,0,0);

$this->SetY($next_y);
$this->Ln();
$this->Cell( 65, 5,'REF No: '.$bill_rec->refnum, 'LTR');

$this->SetFont('Arial','UB',12);

$x=$this->GetX();
$y=$this->GetY();	

$this->SetFont('Arial','',10);
$this->Cell( 65, 5,$roe_str, 0,1,'C');

$this->Cell( 65, 5,'BOL No: '.$bill_rec->bill_of_lading_number, 'LR',1);


$this->Cell( 65, 5,'Voyage: '.$bill_rec->voyage_number, 'LR',1);

//$this->SetX($x);
$this->Cell( 65, 5,'Vessel : '.$bill_rec->vessel_name, 'LRB');

$this->SetY($y);
$this->Cell( 70, 5,'', 0,0);
$this->SetFont('Arial','',8);
$this->Cell( 83, 7,' Agents For :'.$bill_rec->agent, 'T',1);
//$this->Ln();
$this->Cell( 70, 5,'', 0,0);
$this->SetFont('Arial','BU',8);
$this->Cell( 65, 5,' Customer', 0,1);
$this->SetFont('Arial','',8);
$this->Cell( 70, 5,'', 0,0);
$this->Cell( 65, 5,$bill_rec->consignee_name, 0,1);
$this->Cell( 70, 5,'', 0,0);
$x=$this->GetX()+80;
$y=$this->GetY();	
$this->MultiCell( 90, 5, $bill_rec->consignee_address, 0,'L');
$next_y=$this->GetY();
//$this->SetY($y+5);


//$this->SetY($next_y-7);

}

function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Text color in gray
    $this->SetTextColor(128);
    // Page number
 /*   $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C'); */
}


}

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
         header("HTTP/1.1 401 Unauthorized");
          exit('invalid token');
        
  }



    //Get Company 
    $sql="SELECT  * from company limit 1";
    $company = mysqli_query($mysqli,$sql);
    $company_rec= mysqli_fetch_object($company);	
    
    
  $service_type ="charges";
  if ( $request[1]=='reprint') {
    $rec_id=$request[2];
    $service_type ="reprint";
  }
  
  $roe_str='';
  $currency_code='JMD';
  $payment_type='';
  $payment_type_no='';

  $bl_id=preg_replace('/[^a-z0-9_]+/i','',$request[0]);
  $filter=preg_replace('/[^a-z0-9_]+/i','',$request[2]);
  require_once '../service/billoflading_Service.php';
  
  if ( $request[1]=='check'){
    echo '[';
    $pass= false;
    foreach($service_result as $val) {
      if($pass) echo ',';
      echo json_encode($val);
      $pass=true; 
    }
    echo ']';
    exit();
  }

  //Get detail
$detsql="SELECT measure,measure_unit,b.Description_of_goods,package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$bill_rec->id;
$details = mysqli_query($mysqli,$detsql);
if (!$details) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
 }
 $goods_description=' ';
 for ($i=0;$i<mysqli_num_rows($details);$i++) {
  $detail_rec= mysqli_fetch_object($details);
  $goods_description=$goods_description.'
  '.$detail_rec->number_of_items.' '.$detail_rec->description;   
 }

//$pdf = new PDF('L','mm',array(177.8,215.9));
$pdf = new PDF('P','mm',array(177.8,177.8));
//$pdf = new PDF();
//$pdf->AddPage('P',array(215.9,177.8),0);

  mysqli_autocommit($mysqli,FALSE);
  $date = new DateTime('NOW');  
  if (!$new_receipt ){
    
    $date = new DateTime($service_result[0][date].' '.sprintf('%06d',$service_result[0][time]));
  }
  $receipt_number=$service_result[0][receipt_number]; 
  $sql="SELECT s.id,u.user_name,u.full_name FROM `shipment_order` as s left join user_profile as u on s.created_by=u.id where (cancelled is null or cancelled=0) and billoflading_id=".$bill_rec->id;
  $order = mysqli_query($mysqli,$sql);
  if (mysqli_num_rows( $order) ==0){
    //header("HTTP/1.1 428 Precondition Required");
    //exit('No order generated');
  }

  foreach ($receipt_array as $key => $rec_array ) {   
    $service_result=$rec_array["details"]; 
    if ($service_result==null) continue;
    //var_dump($receipt_array);
//echo "<br><br>".$key;     
//var_dump($service_result);
  $show_discount=false;
  if ($rec_array[total_discount]>0) $show_discount=true;
        $reprint="REPRINT";
        if ($request[1]=='create'){
          if ($new_receipt){
            
            $payment_type=$post_data["payment_type"];
            $payment_type_no=$post_data["payment_type_no"];
            $reprint="";
            $receipt_total=$rec_array[total];
            if($rec_array[currency]==1 ){
               $roe_str='';
               if($service_result[0][item_currency]==2) {  $roe_str='ROE @ '.$us_rate->exchange_rate; }
               $currency_code='JMD';              
            }else{
              $roe_str='';
              $currency_code='USD'; 
            }
              $user_id_num=$post_data["cus_id"];
              $sql = " INSERT INTO receipt (payment_type_no,payment_type,receipt_date,receipt_time,payee,receipt_total,currency_id,local_total,created_by,billoflading_id,cancelled,exchange_rate,customer_identification)";
              $sql =$sql. " VALUES ('".$payment_type_no."','".$payment_type."',".date('Ymd').",".date('his').",'".mysql_real_escape_string($bill_rec->consignee_name)."',".round($receipt_total,2).",".$rec_array[currency].",".round($receipt_total,2).",".$claims['id'].",".$bill_rec->id.",0,".$us_rate->exchange_rate.",'".$user_id_num."')"; //TBC created by      
      //echo $sql;
     // continue;          
              $result = mysqli_query($mysqli,$sql);
              if (!$result) {
                http_response_code(404);
                if (!$result) { handleRollBack($mysqli);}
                die($sql.'<br>'.mysqli_error($mysqli));
              }
              $rc_id=mysqli_insert_id($mysqli);
              $receipt_number=$rc_id;
              $reprint="";
              $sql = " INSERT INTO receipt_detail (currency_amount,receipt_id,bol_id,charge_item_id,amount,discount,comment) ";

              $sql =$sql. " VALUES (".$service_result[0][currency_amount].",".$rc_id.",".$bl_id.",".$service_result[0][item].",".$service_result[0][amount].",".$service_result[0][discount].",'')";
       
              for ($i = 1; $i < count($service_result); $i++) {
                  $sql =$sql. " , (".$service_result[$i][currency_amount].",".$rc_id.",".$bl_id.",".$service_result[$i][item].",".$service_result[$i][amount].",".$service_result[$i][discount].",'')";    
              }
              $result = mysqli_query($mysqli,$sql);
              if (!$result) {
                http_response_code(404);       
                if (!$result) { handleRollBack($mysqli);}     
                die('<br>'.$sql.'<br>'.mysqli_error($mysqli));
              }
              $sql=" update bill_of_lading set receipt_processed=1 where id=".$bl_id;
              $result = mysqli_query($mysqli,$sql);
              if (!$result) { handleRollBack($mysqli);}
              if($post_data["no_order"]==true){
                $sql=" update bill_of_lading set order_processed=1 where id=".$bl_id;
                $result = mysqli_query($mysqli,$sql);
                if (!$result) { handleRollBack($mysqli);}
              }
              mysqli_commit($mysqli);
          }

        }

    
    $rate_str='Rate';
    $discount_str='Discount';
    if(!$show_discount){
      $rate_str='';
      $discount_str='';
    }

    $pdf->AddPage();
    //$pdf->Ln();
    $pdf->MultiCell( 100, 5,$goods_description, 0,1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell( 50, 5,'Item Description ', 'B');
    $pdf->Cell( 40, 5,$rate_str, 'B',0,'R');
    $pdf->Cell( 30, 5,$discount_str, 'B',0,'R');
    $pdf->Cell( 40, 5,'Amount ', 'B',1,'R');
    $pdf->SetFont('Arial','',10);
    $total=0;  
    foreach($service_result as $value){
      $rate=number_format($value["discount"]+$value["currency_amount"],2);
      $discount=$value["discount"];
      $us_amt_str='';
      if(!$show_discount) {$rate=''; $discount='';}
      if ($currency_code=='JMD' && $value["item_currency"]==2){
        //$us_amt_str='US$ '.$rate.' '.$roe_str;
        $rate='US$'.number_format($value["discount"]+$value["currency_amount"],2).' '.$roe_str;
       // $pdf->Cell( 60, 8,$us_amt_str,0,1);
      }
      $pdf->Cell( 60, 8,$value["description"]);
      $pdf->SetFont('Arial','',10);
      //$pdf->Cell( 20, 8,$us_amt_str, 0,0,'C');
      
      $pdf->SetFont('Arial','',10);
      $pdf->Cell( 30, 6,$rate, 0,0,'R');
      $pdf->Cell( 30, 6,number_format($discount,2), 0,0,'R');
      $pdf->Cell( 40, 6,number_format($value["amount"],2), 0,1,'R');
      $total +=$value["amount"];
    }
    $pdf->SetY(110);
    $pdf->Cell( 18, 10,'Cashier :', 'T',0);
    $pdf->Cell( 42, 10,$user_name, 'T',0); //TBC
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell( 40, 10,'Receipt Total', 'T',0,'R');    
    $pdf->Cell( 60, 8,number_format($total,2), 'T',1,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell( 130, 8,$payment_type_no, 0,0,'R');
    $pdf->Cell( 30, 8,'('.$payment_type.')', 0,1,'R');
    $pdf->SetFont('Arial','B',10);    
    $pdf->Cell( 160, 10,'CUSTOMER SERVICE OUR FIRST PRIORITY', 0,0,'C');
  
}
if ($request[1]=='blob'){
  $pdf->Output();
}else{   
  $filename="./printout/".$claims['user']."_print.pdf";
  $pdf->Output($filename,'F');
 echo "/printout/".$claims['user']."_print.pdf";
}

function handleRollBack($mysqli){
  mysqli_rollback($mysqli);
  die(mysqli_error($mysqli));
}

?>
