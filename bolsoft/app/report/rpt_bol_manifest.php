<?php
require('fpdf.php');
require_once 'numbersToWords.php';

/*
parm startDate/endDate/grouping/groupid
eg. 20180101/20180202/byuser/3
*/

class PDF extends FPDF
{

  // Simple table
function ItemGroupHeader()
{
    global $current_rec;
    global $containers;
    $this->SetFont('Arial','',9);
    $this->Ln();
    
    $this->Cell(50,5,'Master BL :'.$current_rec["master_bl"],0,0,'L',false);
    $this->Cell(50,5,'Container# :'.$containers[$current_rec["master_bl"]],0,0,'L',false);
    $this->Ln();    
    $this->SetFillColor(220,220,220);
    $this->Cell(15,5,'BL #',0,0,'L',true);
    $this->Cell(50,5,'Shipper',0,0,'L',true);
    $this->Cell(50,5,'Consignee',0,0,'L',true);    
    $this->Cell(45,5,'Description',0,0,'L',true);
    $this->Cell(40,5,'Charges (JMD)',0,1,'L',true);
    //$this->Cell(30,5,'Amount',0,1,'R',true);

}
function ItemGroupFooter()
{
    global $current_rec;
    $this->SetFont('Arial','UB',14); 
    $this->Cell(50,7,' Total for'.$current_rec["description"],0,0);
    $this->Cell(50,7,' $'.$group_total,0,1);
    $this->SetFont('Arial','',12);    
}

function Header()
{
  global $voyage_rec;
  global $company_rec;
  global $bill_rec;
  global $run_date;
  global $receipt_number;
  global $table_data;
  global $table_index;
  global $group_total;
  global $request;
  global $report_filter;
  global $current_rec;  
  global $containers;

$this->SetFont('Arial','',9);  
$this->Cell( 30, 5,$run_date->format('d/m/Y'), 0,'L');
$this->SetFont('Arial','B');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$this->SetFont('Arial','B',18);
//$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,$company_rec->company_name,0,0,'C');
$this->Cell(1,5,'',0,1,'C');
$this->SetFont('Arial','',9);
$this->Cell( 30, 5,$run_date->format('h:i a'), 0,'L');
$x=$this->GetX();
$y=$this->GetY();
$this->Ln();
$this->Cell(20, 5,'Vessel: ', 0,0,'R');
$this->Cell(35, 5,$voyage_rec->vessel_name, 0,1,'L');
$this->Cell(20, 5,'Voyage: ', 0,0,'R');
$this->Cell(40, 5,$voyage_rec->voyage_number, 0,1,'L');
$this->Cell(20, 5,'Reported Date: ', 0,0,'R');
$this->Cell(50, 5,$voyage_rec->arrival_date->format('d/m/Y'), 0,1,'L');
$this->SetXY($x,$y);
$this->Cell( 150, 5,'', 0);
$this->Cell(1,5,'',0,1);
$this->SetFont('Arial','',9);
$this->Cell( 55, 5,'', 0,'L');	
$this->MultiCell( 150, 5, $company_rec->company_address, 0,'l');


$this->SetFont('Arial','UB');
$this->Cell( 200, 5,'Manifest Listing ', 0,1,'C');
$this->SetFont('Arial','');

$this->Ln(); // in the abscence of a company address
$this->ItemGroupHeader();
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
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}


}

global $voyage_rec;
global $company_rec;
global $bill_rec;
global $run_date;
global $receipt_number;
global $table_data;
global $table_index;
global $group_total;
global $request;
global $report_filter;

global $current_rec;
global $containers;

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
$mysqli=$this->db->conn;
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

  $cur_date= date('Ymd') ;

  $sql="SELECT * from charge_item_rate where charge_item_id=-1 and effective_date <=".$cur_date."  order by effective_date desc limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $gct_rec= mysqli_fetch_object($result);	

  // EXchange Rate 
  $sql="SELECT * from currency_rate where currency_id=2 and effective_date <=".$cur_date." order by effective_date desc limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $us_rate= mysqli_fetch_object($result);	

  //Get Company 
  $sql="SELECT  * from company limit 1";
 $company = mysqli_query($mysqli,$sql);
 $company_rec= mysqli_fetch_object($company);	
 $voyage_id= $this->requestUrl[2];
   //Get Voyage 
   $sql="SELECT a.id,a.voyage_number,a.arrival_date,v.vessel_name FROM `voyage` as a left join vessel as v on a.vessel_id=v.id WHERE a.id=$voyage_id";
   $voyage = mysqli_query($mysqli,$sql);
   $voyage_rec= mysqli_fetch_object($voyage);	
   $voyage_rec->arrival_date= new DateTime($voyage_rec->arrival_date);

   //Get charges 
   $sql="SELECT b.attract_gct,b.currency_id,b.billoflading_id,amount,item_code,description FROM `bill_of_lading_other_charge` as b left join charge_item as c on b.charge_item_id=c.id WHERE b.billoflading_id in (select id from bill_of_lading where voyage_id=$voyage_id)";
   $charges = mysqli_query($mysqli,$sql);
   $charges_data = array();
   foreach($charges as $charge_rec){ 
        if(!isset($charges_data[$charge_rec["billoflading_id"]])) {
          $charges_data[$charge_rec["billoflading_id"]] = array();
        }
        $charges_data[$charge_rec["billoflading_id"]][] = $charge_rec;

    }
   
    //containers 
    $sql="SELECT container_number,bill_of_lading_number FROM bill_of_lading as b left join `bill_of_lading_container` as c on b.id=c.billoflading_id where voyage_id=$voyage_id and parent_bol=1 ";
    $tmp = mysqli_query($mysqli,$sql);
    $containers = array();
    foreach($tmp as $rec){
      $containers[$rec["bill_of_lading_number"]]=$rec["container_number"];        
            
    }

  //Get details
   $sql="SELECT * FROM `bill_of_lading_detail` as b WHERE b.billoflading_id in (select id from bill_of_lading where voyage_id=$voyage_id)";
   $result = mysqli_query($mysqli,$sql);
   $details_data = array();
   foreach($result as $detail_rec){ 
        if(!isset($details_data[$detail_rec["billoflading_id"]])) {
          $details_data[$detail_rec["billoflading_id"]] = array();
        }
        $details_data[$detail_rec["billoflading_id"]][] = $detail_rec;

    }

 $sql="SELECT data_value from system_values where code='freight_id'";
 $result = mysqli_query($mysqli,$sql);
 $freight= mysqli_fetch_object($result);	

   $order_number=0;
   $user=$claims['full_name'];



 $sql="SELECT b.id,m.master_bl, b.`bill_of_lading_number`,`consignee_name`,`consignee_address`,`consignee_phone_num`,`shipper_name`,`shipper_address`,`shipper_phone_num`,`notify_name`,`notify_address`,`notify_phone_num` FROM `bill_of_lading` as b left join (select id,bill_of_lading_number as master_bl from bill_of_lading) as m on b.master_bol_id=m.id WHERE voyage_id=$voyage_id and b.parent_bol=0 order by m.master_bl,b.id " ;
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}


$run_date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$total_amount=0;
$table_index=0;
$master_bl='';
$pdf->SetFont('Arial','',9);   
foreach($table_data as $current_rec){
  
  $table_index++;  
  /*
    $this->Cell(50,5,'Consignee',0,0,'C',true);
    $this->Cell(50,5,'Shipper',0,0,'L',true);
    $this->Cell(50,5,'Notify',0,0,'L',true);
    $this->Cell(50,5,'Charges',0,0,'L',true);
    $this->Cell(30,5,'Amount',0,1,'R',true);
*/
if($table_index>1){
   $pdf->Cell(1,1,'',0,1);
   $pdf->Ln();
}

 $y=$pdf->GetY();  
  if ($y>245 || ($master_bl!=$current_rec["master_bl"] && $table_index>1)){
    $pdf->AddPage();
  }
  $master_bl=$current_rec["master_bl"];
  
  $pdf->Cell(15,5,$current_rec["bill_of_lading_number"],0,0,'L');
  $pdf->Cell(50,5,substr($current_rec["shipper_name"],0,22),0,0,'L');
  $pdf->Cell(50,5,substr($current_rec["consignee_name"],0,22),0,0,'L');
  $pdf->Cell(45,5,'',0,0,'L');    
  $bl_y= $pdf->GetVerticalPosition(); // $addressPos["y"]=$addressPos["y"]+5;
  $x=$pdf->GetX();
  $charges= $charges_data[$current_rec["id"]][0]["item_code"];
  $pdf->Cell(20,5,$charges,0,0,'L');
  $total=0;
  $amount =$charges_data[$current_rec["id"]][0]["amount"];  
  if($charges_data[$current_rec["id"]][0]["currency_id"]==2){ $amount=$amount* $us_rate->exchange_rate;}
  $total+=$amount;
  $pdf->Cell(15,5,number_format($amount,2),0,1,'R');
  $gct_total=0;
  if ($charges_data[$current_rec["id"]][0]["attract_gct"]=="1") {$gct_total+=$amount;} 
  $address_y= $pdf->GetVerticalPosition();  
  for ($i = 1; $i < count($charges_data[$current_rec["id"]]); $i++) {
    $pdf->SetX($x);
    $charge= $charges_data[$current_rec["id"]][$i]["item_code"];
    $pdf->Cell(20,5,$charge,0,0,'L');
    $amount =$charges_data[$current_rec["id"]][$i]["amount"];    
    if($charges_data[$current_rec["id"]][$i]["currency_id"]==2){ $amount=$amount* $us_rate->exchange_rate;}
    if ($charges_data[$current_rec["id"]][$i]["attract_gct"]=="1") {$gct_total+=$amount;} 
    $total+=$amount;
    $pdf->Cell(15,5,number_format($amount,2),0,1,'R');
   }
   if($gct_total>0){
     $pdf->SetX($x);
     $gct_amt=$gct_total*.01 * $gct_rec->rate;
     $total +=$gct_amt;     
     $pdf->Cell(20,5,'GCT',0,0,'L');
     $pdf->Cell(15,5,number_format($gct_amt,2),0,1,'R');
   }
   $pdf->SetFont('Arial','B');   
   $pdf->SetX($x);
   $pdf->Cell(20,5,'',0,0,'L');    
   $pdf->Cell(15,5,'$'.number_format($total,2),'T',1,'R');
   $pdf->SetFont('Arial','');   
   $total_amount +=$total;
  $max_y= $pdf->GetVerticalPosition(); 
  //$pdf->SetY($y);  
  //$pdf->Cell(15,5,'',0,0);
  //$x=$pdf->GetX();
  $pdf->SetVerticalPosition($address_y);
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(15,5,'',0,0);
  $x=$pdf->GetX();
  $pdf->MultiCell( 50, 5, $current_rec["shipper_address"].' '.$current_rec["shipper_phone_num"], 0,'L');
  $max_y = $pdf->FurthestVerticalPosition( $pdf->GetVerticalPosition(), $max_y );
  
  $margin_x=$pdf->GetX();
  $x+=50;
  $pdf->SetVerticalPosition($address_y);
  $pdf->SetFont('Arial','',9);
  $pdf->SetX($x);
  $pdf->MultiCell( 50, 5, $current_rec["consignee_address"].' '.$current_rec["consignee_phone_num"], 0,'L');  
  $max_y = $pdf->FurthestVerticalPosition( $pdf->GetVerticalPosition(), $max_y );

  $x+=50;
  $pdf->SetVerticalPosition($bl_y);
  $pdf->SetFont('Arial','',9);
  $pdf->SetX($x);
  for ($i = 0; $i < count($details_data[$current_rec["id"]]); $i++) {
    $pdf->SetX($x);
      $pdf->MultiCell( 45, 5,$details_data[$current_rec["id"]][$i]["Description_of_goods"], 0,'L');
      $max_y = $pdf->FurthestVerticalPosition( $pdf->GetVerticalPosition(), $max_y ); 
  }
  $pdf->SetVerticalPosition($max_y);
  $pdf->SetFont('Arial','',9);
   $pdf->SetX($margin_x);

}
$total_amount;




$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(165,5,'Grand Total',0,0,'R');   
$pdf->Cell(30,5,'$'.number_format($total_amount,2),0,0,'R');   

$pdf->Output();

?>
