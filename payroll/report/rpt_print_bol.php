<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';
 error_reporting(E_ERROR | E_PARSE);
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
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


$order_number=0;
   $user=$claims['full_name'];
   $sql="SELECT s.id,u.user_name,u.full_name FROM `shipment_order` as s left join user_profile as u on s.created_by=u.id where (cancelled is null or cancelled=0) and billoflading_id=".$request[0];
   $order = mysqli_query($mysqli,$sql);
   if (mysqli_num_rows( $order) >0){
       $order_rec= mysqli_fetch_object($order);	
       $order_number=$order_rec->id;
       $user=$order_rec->full_name;
   }
    
  // excecute SQL statement
  $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$request[0];
 $result = mysqli_query($mysqli,$sql);
 $rec= mysqli_fetch_object($result);	

  // get container
   $sql="SELECT * FROM `bill_of_lading_container` WHERE `billoflading_id`=".$rec->master_id." limit 1";
  $con = mysqli_query($mysqli,$sql);
  $con_rec= mysqli_fetch_object($con);	

// get Ports
$sql="select y.port_name as delivery,d.port_name as discharge,o.port_name as origin,l.port_name as loading from ((((bill_of_lading as b left join port as o on b.port_of_origin=o.id) left join port as l on b.port_of_loading=l.id) left join port as d on b.port_of_discharge=d.id) left join port as y on b.port_of_delivery=y.id) WHERE b.id=".$request[0];
$result = mysqli_query($mysqli,$sql);
$port_rec= mysqli_fetch_object($result);	

// get Detail
$sql="select Description_of_goods from bill_of_lading_detail where  billoflading_id=".$request[0];
$details = mysqli_query($mysqli,$sql);

// die if SQL statement failed
if (!$details) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

//Get detail
$sql="SELECT package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$request[0];
$detail = mysqli_query($mysqli,$sql);
$detail_rec= mysqli_fetch_object($detail);	
if (!$detail) {
 http_response_code(404);
 die($sql.'<br>'.mysqli_error($mysqli));
}


//echo $f->format(1432);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Ln();$pdf->Ln();
$pdf->Cell(0,10,'BILL OF LADING    ',0,1,'C');
$pdf->SetTextColor(0,0,255);
$pdf->Cell(0,10,$rec->refnum,0,1,'C');
$pdf->SetTextColor(0,0,0);
$pdf->Ln();
$pdf->Cell(40,10,'Voyage',0,0);
$pdf->Cell(40,10,$rec->voyage_number,0,0);
$pdf->Cell(40,10,substr($rec->arrival_date,6,2).'-'.substr($rec->arrival_date,4,2).'-'.substr($rec->arrival_date,2,2),0,1,'C');

$pdf->Cell(40,10,'Vessel',0,0);
$pdf->Cell(100,10,$rec->vessel_name,0,1);
$pdf->Ln();

$pdf->Cell(40,10,'Port of Origin',0,0);
$pdf->Cell(100,10,$port_rec->origin,0,1);
$pdf->Cell(40,10,'Port of Discharge',0,0);
$pdf->Cell(100,10,$port_rec->discharge,0,1);
$pdf->Cell(40,10,'Port of Loading',0,0);
$pdf->Cell(100,10,$port_rec->loading,0,1);
$pdf->Cell(40,10,'Port of Delivery',0,0);
$pdf->Cell(100,10,$port_rec->delivery,0,1);
$pdf->Ln(40);

for ($i=0;$i<mysqli_num_rows($details);$i++) {
  $rec= mysqli_fetch_object($details);
  $pdf->Cell(100,10,$rec->Description_of_goods,0,1);
} 

$pdf->Output();
?>
