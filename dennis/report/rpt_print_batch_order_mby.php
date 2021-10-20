<?php
/*
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
$ids=$input['ids'];
$new_orders_only=$input['new_orders_only'];

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
*/
  //Get Company 
  $sql="SELECT  * from company limit 1";
 $company = mysqli_query($mysqli,$sql);
 $company_rec= mysqli_fetch_object($company);	
/*
   //Get order number
   $sql="SELECT id FROM `shipment_order` order by id desc limit 1 ";
   $order = mysqli_query($mysqli,$sql);
   $order_rec= mysqli_fetch_object($order);	
   $order_number=(1+$order_rec->id);
*/

$pdf = new FPDF('P','mm',array(177.8,177.8));
//$pdf = new FPDF('P','mm',array(215.9,177.8));

foreach ($ids as $bl_id) {
 
$order_number=0;
$reprint='';
   $user=$claims['full_name'];
   $sql="SELECT s.order_date,s.id,u.user_name,u.full_name FROM `shipment_order` as s left join user_profile as u on s.created_by=u.id where (cancelled is null or cancelled=0) and billoflading_id=".$bl_id;
   $order = mysqli_query($mysqli,$sql);
   if (mysqli_num_rows( $order) >0){
       $order_rec= mysqli_fetch_object($order);	
       $order_number=$order_rec->id;
       $reprint='REPRINT';
       $order_date= new DateTime($order_rec->order_date);
       $user=$order_rec->full_name;
   }
    
  // excecute SQL statement
  $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,mv.vessel_name as mby_vessel,v.mby_arrival_date,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM (((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join vessel as mv on v.mby_vessel_id=mv.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$bl_id;
  //$sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$bl_id;
 $result = mysqli_query($mysqli,$sql);
 $rec= mysqli_fetch_object($result);	

  // get container
   $sql="SELECT * FROM `bill_of_lading_container` WHERE `billoflading_id`=".$rec->master_id." limit 1";
  $con = mysqli_query($mysqli,$sql);
  $con_rec= mysqli_fetch_object($con);	

  
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

//Get detail
$sql="SELECT measure,measure_unit,b.Description_of_goods,package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$bl_id;
$details = mysqli_query($mysqli,$sql);
if (!$details) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
 }
$detail_desc="";
for ($i=0;$i<mysqli_num_rows($details);$i++) {
    $detail_rec= mysqli_fetch_object($details);
    $detail_desc=$detail_desc.$detail_rec->Description_of_goods."\n";
    $detail_desc=$detail_desc.'Measure :'.$detail_rec->measure.$detail_rec->measure_unit.'    Weight :'.$detail_rec->weight.$detail_rec->weight_unit."\n";
} 

if($order_number==0){
  $user =$claims['id'];
  date_default_timezone_set('America/Jamaica');
  $date= date('Ymd') ;
  $sql = "INSERT INTO `shipment_order` (`voyage_id`, `billoflading_id`, `created_by`, `locked`, `printed`, `cancelled`, `order_date`, `cancel_by`, `cancel_date`)   VALUES ($rec->voyage_id, $rec->id, $user, 0,1, 0, $date,'',0)";
  $result = mysqli_query($mysqli,$sql);
  $order_number= mysqli_insert_id($mysqli);
  if (!$result) {
    http_response_code(404);
    die($sql.'<br>'.mysqli_error($mysqli));
  }
  $sql=" update bill_of_lading set order_processed=1 where id=".$bl_id;
  $result = mysqli_query($mysqli,$sql);
  $user=$claims['full_name'];
  
}else{
  if ($new_orders_only==1){
    continue;
  }
}


$pdf->AddPage();
$pdf->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$pdf->SetFont('ManuskriptGothisch','',26);
$pdf->Image('../../img/dennis-icon.jpg',10,10,-300);
$pdf->Cell(30,10,'',0,0); $pdf->SetTextColor(0,0,255);
$pdf->Cell(65,10,'Dennis Shipping',0,0);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',10);

$pdf->Cell(25,5,'Order No.',0,0,'R');
$pdf->SetTextColor(255,0,0);
$pdf->Cell(30,5,sprintf("%08d",$order_number),0,1,'L');
$pdf->SetTextColor(0,0,0);
$y=$pdf->GetY();	
$pdf->Ln();
$pdf->Cell(30,5,'',0,0);
$x=$pdf->GetX()+30;
$pdf->MultiCell( 70, 5, $company_rec->company_address, 0,'L');

$pdf->SetY($y); //set pointer back to previous values
$pdf->SetX($x);
$pdf->Cell(60,5,'Issued By',0,0,'R');
$pdf->Cell(45,5,$user,0,1,'L');  
$pdf->SetTextColor(255,0,0);
$pdf->Cell(130,5,$reprint,0,0,'R');
$pdf->SetTextColor(0,0,0);
$pdf->Ln(17);
$pdf->Cell(60,10,'Montego Bay Jamaica',0,0);
$pdf->SetFont('','U');
date_default_timezone_set('America/Jamaica');
$pdf->Cell(100,10,$order_date->format('M d, Y'),0,0);
$pdf->SetFont('','');
$pdf->Ln();
$pdf->Cell(60,10,'Please Deliver to',0,0);
$pdf->SetFont('','U');
$pdf->Cell(100,10,$rec->consignee_name,0,0);
$pdf->SetFont('','');
$pdf->Ln();

$pdf->Cell(60,5,'TO THE WHARFINGER AT',0,0);
$pdf->SetFont('','U');
$pdf->Cell(100,5,$rec->port_name,0,1);


$pdf->Cell(65,10,'Voy :'.$rec->voyage_number,0,0);
$pdf->SetFont('','B');
$pdf->Cell(42,10,'Arrival Dates',0,1);
$pdf->Cell(18,5,'EX. S/S:',0,0);
$pdf->SetFont('','U');
$pdf->Cell(42,5,$rec->vessel_name,0,0);
$pdf->Cell(40,5,' KGN:'.substr($rec->arrival_date,2,2).'-'.substr($rec->arrival_date,4,2).'-'.substr($rec->arrival_date,6,2),0,0);
$pdf->Cell(30,5,'B/L Number :'.$rec->bill_of_lading_number,0,1);
$pdf->Cell(26,5,'MBY Vessel:',0,0);
$pdf->Cell(34,5,$rec->mby_vessel,0,0);
$pdf->Cell(40,5,' MBY:'.substr($rec->mby_arrival_date,2,2).'-'.substr($rec->mby_arrival_date,4,2).'-'.substr($rec->mby_arrival_date,6,2),0,1);
$pdf->SetFont('','');
$pdf->Ln();

$pdf->SetFont('','B');
$pdf->Cell(28,5,'CONSIGNEE:',0,0);
$pdf->SetFont('','');
$pdf->Cell(100,5,$rec->consignee_name,0,1);
$y=$pdf->GetY();
$x=$pdf->GetX()+70;
$pdf->MultiCell( 70, 5, $rec->consignee_address, 0,1);
$pdf->Ln();
$pdf->SetFont('','B');
$pdf->Cell(22,5,'SHIPPER:',0,0);
$pdf->SetFont('','');
$pdf->Cell(100,5,$rec->shipper_name,0,1);
$pdf->MultiCell( 70, 5, $rec->shipper_address, 0,1);

$y2=$pdf->GetY();
$pdf->SetY($y); //set pointer back to previous values
$pdf->SetX($x);
$pdf->Cell(5,5,'',0,0);
$pdf->MultiCell( 100, 5, $detail_desc, 0,1);
if ($y2 > $pdf->GetY()) {$pdf->SetY($y2);}
$pdf->Ln();
$pdf->Cell(50,5,'Dr Number : '.$rec->refnum,0,1);
$pdf->Cell(60,5,'Container#: '.$con_rec->container_number,0,0);
$pdf->Cell(50,5,'Per : ____________________________',0,1);


}
if ($request[0]=='blob'){
  $pdf->Output();
}else{   
  $filename="./printout/".$claims['user']."_print.pdf";
  $pdf->Output($filename,'F');
 echo "/printout/".$claims['user']."_print.pdf";
}
?>
