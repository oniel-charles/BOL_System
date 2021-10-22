<?php
require('fpdf.php');
//require_once '../user_access.php';
//require_once '../numbersToWords.php';
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
$mysqli=$this->db->conn;
mysqli_set_charset($mysqli,'utf8'); 

$claims=authenticateToken();
if ($claims==null){
         header("HTTP/1.1 401 Unauthorized");
          exit('invalid token');
          
  }

  // excecute SQL statement
  $sql="SELECT b.value_of_goods,p.id as master_id,p.consignee_name as master_name,p.consignee_address as master_address,pl.port_name as loading,pd.port_name as discharge,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.notify_name,b.notify_address,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM (((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  as pd on b.port_of_discharge=pd.id) left join port  as pl on b.port_of_loading=pl.id) left join (select id,bill_of_lading_number,consignee_name,consignee_address from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$this->requestUrl[2];
 $result = mysqli_query($mysqli,$sql);
  
// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

//Get detail
$detsql="SELECT measure,measure_unit,b.Description_of_goods,package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$this->requestUrl[2];
$details = mysqli_query($mysqli,$detsql);
if (!$details) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
 }
 	

//$detail_rec= mysqli_fetch_object($detail);	
$rec= mysqli_fetch_object($result);	

//charges
$chgarr = array();
$sql="SELECT b.amount,c.item_code,b.charge_item_id FROM bill_of_lading_other_charge as b left join charge_item as c on b.`charge_item_id`=c.id where billoflading_id=".$request[0];
$result = mysqli_query($mysqli,$sql);
for ($i=0;$i<mysqli_num_rows($result);$i++) {
    $chg= mysqli_fetch_object($result);	  
    $chgarr[$chg->item_code] = $chg->amount; 
}

  // get container
  $sql="SELECT * FROM `bill_of_lading_container` WHERE `billoflading_id`=".$rec->master_id." limit 1";
  $con = mysqli_query($mysqli,$sql);
  $con_rec= mysqli_fetch_object($con);	

//echo $f->format(1432);

$pdf = new FPDF();
//$pdf = new FPDF('L','mm',array(200,300));
$pdf = new FPDF('P','mm','Letter');
$pdf->AddPage();
$pdf->SetFont('Arial','',16);
$pdf->Line(7,05,208,05);
$pdf->Line(7,05,7,265);
$pdf->Line(208,05,208,265);
$pdf->Line(7,265,208,265);

$pdf->SetY(7);
$pdf->Cell(120,7,$rec->master_name,0,1);
$pdf->MultiCell( 120,7,$rec->master_address,0,1);
$y=$pdf->GetY();
$pdf->SetY(25);
$pdf->SetX(140);

$pdf->SetFont('Arial','I',13);
$pdf->Cell(70,10,'ORIGINAL BILL OF LADING',0,1);

$pdf->SetFont('Arial','B',8);
$pdf->Line(7,35,208,35);
$pdf->Line(104,35,104,121); //vertical
$pdf->Line(104,43,208,43);
$pdf->Line(104,51,208,51);
$pdf->Line(7,59,208,59); //straight across

$pdf->SetY(35);
$pdf->Cell(94,5,'SHIPPER / EXPORTER',0,1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(94,5,$rec->shipper_name,0,1);
$pdf->MultiCell( 94, 5, $rec->shipper_address, 0,1);

$pdf->SetY(35);
$pdf->SetX(104);
$pdf->Cell(40,8,'DOCUMENT NO#:',0,0);
$pdf->Cell(60,8,$rec->refnum,0,1);

$pdf->Cell(94,8,'',0,0);
$pdf->Cell(40,8,'PORT OF LOADING:',0,0);
$pdf->Cell(60,8,$rec->loading,0,1);
$pdf->Cell(94,8,'',0,0);
$pdf->Cell(40,8,'PORT OF DISCHARGE:',0,0);
$pdf->Cell(60,8,$rec->discharge,0,1);

$pdf->Line(7,83,104,83); 
$pdf->Line(7,107,208,107); //straight across

$pdf->SetY(60);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(94,5,'CONSIGNEE (Complete Name & Address)',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(94,5,'CONTACT AGENT:',0,1);
$pdf->Cell(94,5,$rec->consignee_name,0,1);
$pdf->MultiCell( 94, 5, $rec->consignee_address, 0,1);

$pdf->SetY(84);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(94,5,'NOTIFY PARTY (Complete Name & Address)',0,1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(94,5,$rec->notify_name,0,1);
$pdf->MultiCell( 94, 5, $rec->notify_address, 0,1);

$pdf->SetY(65);
$pdf->SetX(106);
$pdf->SetFont('Arial','',15);
$pdf->Cell(94,7,'UNITED FREIGHT FORWARDERS LTD.',0,1);
$pdf->SetFont('Arial','',12);
$pdf->SetY(72);
$pdf->SetX(106);
$pdf->MultiCell( 94, 7,'128 THIRD STREET, NEWPORT WEST
KINGSTON 13, JAMAICA W.I.
TEL: 876-757-7138 / 876-758-8901 W.I.
FAX: 876-758-8953
unitedfreightf@gmail.com', 0,'C');

$pdf->SetFont('Arial','',10);
$pdf->Line(7,114,104,114); 
$pdf->Line(7,121,208,121); //straight across
$pdf->SetY(106);
$pdf->SetX(104);
$pdf->Cell(40,8,'OTHER DETAILS :',0,0);
$pdf->SetX(8);
$pdf->Cell(60,8,'PLACE OF DELIVERY :  KINGSTON WHARVES',0,1);
$pdf->Cell(40,8,'EX / VESSEL NAME: ',0,0);
$pdf->Cell(44,8,$rec->vessel_name,0,1);

$pdf->Line(7,129,208,129); //straight across
$pdf->Cell(202,6,'VESSEL DATA AND SHIPPER PARTICULARS',0,1,'C');
$pdf->Line(7,136,208,136); //straight across

$pdf->SetFont('Arial','',9);
$pdf->Line(52,129,52,204); //vertical
$pdf->Line(78,129,78,204); //vertical
$pdf->Line(148,129,148,204); //vertical
$pdf->Line(175,129,175,204); //vertical
$pdf->Cell(43,10,'MARKS AND NUMBERS',0,0);
$pdf->Cell(25,10,'NO. OF PKGS.',0,0);
$pdf->Cell(70,10,'DESCRIPTION OF PACKAGES & GOODS',0,0,'C');
$pdf->Cell(30,10,'GROSS WGHT.',0,0,'C');
$pdf->Cell(27,10,'MEAUSUREMENT',0,1,'C');

//write details
$detail_desc="";
for ($i=0;$i<mysqli_num_rows($details);$i++) {
    $detail_rec= mysqli_fetch_object($details);

    $pdf->Cell(43,5,"DR # ".$rec->refnum,0,0);
    $pdf->Cell(25,5,$detail_rec->number_of_items,0,0,'C');
    $y=$pdf->GetY();
    $x=$pdf->GetX()+70;
    $pdf->MultiCell( 70, 5,$detail_rec->Description_of_goods, 0,1);
    $pdf->SetY($y);
    $pdf->SetX($x);
    $pdf->Cell(27,5,$detail_rec->weight.'  '.$detail_rec->weight_unit,0,0,'R');
    $pdf->Cell(33,5,$detail_rec->measure.'  '.$detail_rec->measure_unit,0,1,'R');

    $pdf->Cell(43,5,"BL # ".$rec->bill_of_lading_number,0,1);
    $pdf->Cell(43,5,"CN # ".$con_rec->container_number,0,1);
    $pdf->Ln();
 
} 

$pdf->Line(7,204,208,204); //straight across
$pdf->Line(7,213,208,213); //straight across
$pdf->SetY(204);
$pdf->Cell(30,8,'FREIGHT & CHARGES',0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(160,8,'WHARFAGE AND HANDLING CHARGES NOT INCLUDED',0,1,'C');

$pdf->SetY(213);
$pdf->SetFont('Arial','',8);
$pdf->Cell(40,5,'OCEAN FREIGHT.:',0,0);
$pdf->Cell(24,5,'0.00',0,1,'R');
$pdf->Cell(40,5,'HANDLING...........:',0,0);
$pdf->Cell(24,5,$chgarr['HAN'],0,1,'R');
$pdf->Cell(40,5,'INSURANCE.........:',0,1);
$pdf->Cell(40,5,'PAID....................:',0,1);
$pdf->Cell(40,5,'ADDTL. FREIGHT:',0,1);
$pdf->Cell(40,5,'BALANCE............:',0,1);
$pdf->SetY(245);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell( 85, 4,"GLASS AND PERISHABLE ITEMS WOULD BE SHIPPED AT
OWNERS RISK.
THANK YOU FOR YOUR SERVICE", 0,'C');

$pdf->SetY(210);
$pdf->SetX(94);
$pdf->SetFont('Arial','',5);
$pdf->MultiCell( 110, 4,"
IN ACCEPTING THIS BILL OF LADING ANY LOCAL CUSTOMS OR PRIVILEGES TO THE CONTRARY 
NOTWITHSTANDING THE SHIPPER, CONSIGNEE AND OWNER OF THE GOODS AND HOLDER OF THIS 
BILL OF LADING AGREE TO BE BOUND BY ALL THE STIPULATIONS, EXCEPTIONS AND CODITIONS STATED HEREIN.
  CARIBBEAN CARGO & IT'S LOCAL AGENTS UNITED FREIGHT FORWARDERS LTD. ASK THAT YOU 
INSPECT YOUR GOODS FOR DAMAGES OR THEFT BEFORE LEAVING THE PORT AND REPORT TO THE 
WHARFINGER. NO CLAIM WILL BE HONORED BY CARIBBEAN INT'L SHIPPING OR UNITED FREIGHT 
FORWARDERS AFTER CARGO LEFT THE WHARF.
   THANKS FOR CHOOSING CARIBBEAN INTERNATIONAL SHIPPING.", 0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetY(251);
$pdf->SetX(94);
$pdf->Cell(90,8,'AUTHORISED BY:______________________       DATE:_______________________',0,0);

$pdf->Output();
?>
