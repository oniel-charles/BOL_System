<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
//var_dump($input); 
// connect to the mysql database
if (mysqli_connect_errno()){
  header("HTTP/1.1 401 Unauthorized");
  exit(mysqli_connect_error());
}
mysqli_set_charset($mysqli,'utf8'); 

$claims=authenticateToken();
if ($claims==null){
   //      header("HTTP/1.1 401 Unauthorized");
   //       exit('invalid token');
          
  }

  // excecute SQL statement
  $sql="SELECT port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name,b.consignee_phone_num,b.notify_phone_num  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$request[0];
 $result = mysqli_query($mysqli,$sql);
  
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$sql="SELECT package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$request[0];
$detail = mysqli_query($mysqli,$sql);

if (!$detail) {
 http_response_code(404);
 die($sql.'<br>'.mysqli_error($mysqli));
}

$detail_rec= mysqli_fetch_object($detail);	
$rec= mysqli_fetch_object($result);	

date_default_timezone_set('America/Jamaica');
$form_date= new DateTime('NOW');  

//echo $f->format(1432);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Image('../../img/dennis-shipping.jpg',80,20,60);
$pdf->Ln(40);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(200,10,'TRUCKING RECEIPT',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Ln(4);
$pdf->Cell(130,10,'',0,0);
$pdf->Cell(10,10,'Date:',0,0);
$pdf->SetFont('Arial','U',10);
$pdf->Cell(30,10,$form_date->format('M d, Y'),0,1);
$pdf->SetFont('Arial','',10);

$pdf->Cell(190,10,'Receive From ______________________________________________________________________________',0,1);
$pdf->Ln(4);
$pdf->Cell(18,5,'Consignee ',0,0);
$pdf->Cell(100,5,$rec->consignee_name,'B',0);
$pdf->Cell(18,5,'Manifest# ',0,0);
$pdf->Cell(41,5,$rec->voyage_number,'B',1);
$pdf->Ln(8);
$pdf->Cell(35,5,'Vessel ',0,0);
$pdf->Cell(143,5,$rec->vessel_name,'B',1);
$pdf->Ln(8);

$pdf->Cell(10,5,'B/L# ',0,0);
$pdf->Cell(55,5,$rec->refnum,'B',0);
$pdf->Cell(6,5,'Tel ',0,0);
$pdf->Cell(50,5,$rec->consignee_phone_num ,'B',0);
$pdf->Cell(6,5,'Tel ',0,0);
$pdf->Cell(50,5,$rec->notify_phone_num ,'B',1);
$pdf->Ln(8);

$pdf->Cell(30,5,'Delivery Address ',0,0);
$pdf->SetFont('Arial','U',10);
$pdf->MultiCell( 120, 5,$rec->consignee_address , 0,1);
$pdf->SetFont('Arial','',10);
$pdf->Ln(8);

$pdf->Cell(35,5,'Land Mark/Directions ',0,0);
$pdf->Cell(143,5,'','B',1);
$pdf->Cell(178,10,'','B',1);
$pdf->Ln(8);

$pdf->Cell(33,5,'Amount Collected $ ',0,0);
$pdf->Cell(60,5,'','B',0);
$pdf->Cell(10,5,'USD ',0,0);
$pdf->Cell(5,5,' ',1,0);
$pdf->Cell(10,5,'JMD ',0,0);
$pdf->Cell(5,5,' ',1,0);
$pdf->Cell(15,5,'Balance $',0,0);
$pdf->Cell(40,5,' ','B',1);
$pdf->Ln(8);

$pdf->Cell(25,5,'Prepared By ',0,0);
$pdf->Cell(64,5,'','B',0);
$pdf->Cell(25,5,'Delivered By ',0,0);
$pdf->Cell(64,5,'','B',1);
$pdf->Ln(8);

$pdf->Cell(35,5,'Consignee Signature',0,0);
$pdf->Cell(60,5,'','B',1);
$pdf->Ln(8);

$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(255,0,0);
$pdf->MultiCell( 190, 5, 'Please Note: If the location of the home in Jamaica is not accessible by vehicle, the barrel(s)/items(s) CANNOT be delivered at your door. If your home is not readily accessible please inform us prior to delivery. We will not be responsible for any additional cost.', 0,1);



$pdf->Output();
?>
