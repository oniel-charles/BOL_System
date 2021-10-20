<?php
require('fpdf.php');
require('numbersToWords.php');

 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
//var_dump($input); 
// connect to the mysql database
$mysqli=$this->db->conn;
if (mysqli_connect_errno()){
  header("HTTP/1.1 401 Unauthorized");
  exit(mysqli_connect_error());
}

mysqli_set_charset($mysqli,'utf8'); 



  // excecute SQL statement
  $sql="SELECT port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$this->requestUrl[2];
 $result = mysqli_query($mysqli,$sql);
  
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$sql="SELECT package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$this->requestUrl[2];
$detail = mysqli_query($mysqli,$sql);

if (!$detail) {
 http_response_code(404);
 die($sql.'<br>'.mysqli_error($mysqli));
}

$detail_rec= mysqli_fetch_object($detail);	
$rec= mysqli_fetch_object($result);	

//echo $f->format(1432);

$pdf = new FPDF();
$pdf = new FPDF('L','mm',array(200,300));
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Ln(40);
$pdf->Cell(100,10,$rec->consignee_name,0,0);
$pdf->Cell(100,10,$rec->shipper_name,0,0);
$pdf->Ln();
$x=$pdf->GetX()+100;
$y=$pdf->GetY();	
$pdf->MultiCell( 100, 5, $rec->consignee_address, 0,1);
$pdf->SetY($y); //set pointer back to previous values
$pdf->SetX($x);
$pdf->MultiCell( 100, 5, $rec->shipper_address, 0,1);
$pdf->Ln(10);
$pdf->Cell(100,10,substr($rec->arrival_date,2,2).'-'.substr($rec->arrival_date,4,2).'-'.substr($rec->arrival_date,6,2),0,0);
$pdf->Cell(30,10,$detail_rec->weight,0,0); $pdf->Cell(30,10,$detail_rec->weight_unit,0,0); $pdf->Cell(30,10,$detail_rec->number_of_items,0,1);
$pdf->Cell(100,10,$rec->vessel_name,0,0);  $pdf->Cell(100,10,numberTowords($detail_rec->number_of_items),0,1);
$pdf->Cell(100,10,$rec->bill_of_lading_number,0,0); $pdf->Cell(100,10,$detail_rec->description,0,0); $pdf->Cell(50,10,'U.S.A',0,0); $pdf->Cell(100,10,'U.S.A',0,1);
$pdf->Cell(100,10,$rec->port_name,0,0);
//$pdf->Write(5,wordwrap($rec->consignee_address,30,"\n"));
//$pdf->Write(5,wordwrap($rec->shipper_address,70,"\n"));



$pdf->Output();
?>
