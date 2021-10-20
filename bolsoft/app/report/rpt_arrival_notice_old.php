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

      //Get Company 
      $sql="SELECT  * from company limit 1";
      $company = mysqli_query($mysqli,$sql);
      $company_rec= mysqli_fetch_object($company);	

  // excecute SQL statement
  $sql="SELECT p.id as master_id,p.consignee_name as master_name,p.consignee_address as master_address,pl.port_name as loading,pd.port_name as discharge,v.manifest_number,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.notify_name,b.notify_address,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM (((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  as pd on b.port_of_discharge=pd.id) left join port  as pl on b.port_of_loading=pl.id) left join (select id,bill_of_lading_number,consignee_name,consignee_address from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$this->requestUrl[2];
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
$chgarr['FRT']=0;
$sql="SELECT b.amount,c.item_code,c.description,b.charge_item_id FROM bill_of_lading_other_charge as b left join charge_item as c on b.`charge_item_id`=c.id where billoflading_id=".$this->requestUrl[2];
$result = mysqli_query($mysqli,$sql);

for ($i=0;$i<mysqli_num_rows($result);$i++) {
    $chg= mysqli_fetch_object($result);	  
    $chgarr[$chg->item_code] = $chg->amount; 
    if (strripos(strtolower($chg->description), 'freight')) {
       $chgarr['FRT']=$chg->amount; 
    }  
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
$pdf->SetFont('Times','',15);
$pdf->Cell(200,5,'QUALITY ONE',0,1,'C');
$pdf->Cell(200,5,'INTERNATIONAL SHIPPING EXPRESS CORP',0,1,'C');
$pdf->Cell(200,5,'3913 DYRE AVENUE * BRONX, NEW YORK 10466',0,1,'C');
$pdf->Cell(200,5,'TEL: 718-231-1909 * FAX: 718-231-1815',0,1,'C');
$pdf->Ln();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(200,5,'ARRIVAL NOTICE',0,1,'C');
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(27,5,'Bill of Lading:',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,5,$rec->bill_of_lading_number,0,0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(12,5,'HBL:',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,5,$rec->refnum,0,0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(12,5,'MAN:',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,5,$rec->manifest_number,0,1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);

$pdf->Line(7,50,7,265); //vertical side
$pdf->Line(208,50,208,265); //vertical side
$pdf->Line(7,265,208,265); //bottom line

$pdf->Line(7,50,208,50); //top
$pdf->Line(98,50,98,265); //vertical

$pdf->Line(7,83,208,83);  //straight across

$y=$pdf->GetY();	
$pdf->SetFont('Arial','B',12);
$pdf->Cell(94,10,'Shipper Address',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,5,$rec->shipper_name,0,1);
$pdf->MultiCell( 94, 5, $rec->shipper_address, 0,1);

$pdf->SetY($y);
$pdf->SetX(104);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(94,10,'Consignee Address',0,1);
$pdf->SetFont('Arial','',12);
$pdf->SetX(104);
$pdf->Cell(94,5,$rec->consignee_name,0,1);
$pdf->SetX(104);
$pdf->MultiCell( 94, 5, $rec->consignee_address, 0,1);
$pdf->Line(76,83,76,190); // Left of number of pieces vertical
$pdf->Line(76,100,208,100); // horizontal
$pdf->SetY(88);
$pdf->Cell(65,5,'Contact Agent',0,0);
$pdf->Cell(1,5,' ',0,0);
$y=$pdf->GetY();
$x=$pdf->GetX();
$pdf->MultiCell( 23, 5, 'No. of Pieces', 0,1);
$pdf->SetXY($x+23,$y);
$pdf->Cell(55,5,'Description of Packages',0,2);
$y=$pdf->GetY();	
$pdf->Cell(65,5,'Particular furnished by shipper',0,1);
$pdf->SetY($y);
$pdf->MultiCell( 68, 5, $company_rec->company_name, 0,1);
$pdf->MultiCell( 68, 5, $company_rec->company_address, 0,1);
$pdf->Cell(68,5,$company_rec->phone,0,0);
$pdf->SetY($y);
$pdf->Ln();
$pdf->Ln();
$max_y=$pdf->GetY();

//write details
$detail_desc="";
for ($i=0;$i<mysqli_num_rows($details);$i++) {
    $pdf->SetY($max_y);
    $detail_rec= mysqli_fetch_object($details);

    $pdf->SetX(75);
    $pdf->Cell(24,7,$detail_rec->number_of_items,0,0,'C');
    $y=$pdf->GetY();	
    $pdf->MultiCell( 70, 7, $detail_rec->Description_of_goods,0,1);
    $max_y=$pdf->GetY();
    $pdf->SetY($y);
    $pdf->SetX(168);
    $pdf->Cell(20,7,$detail_rec->weight.'  '.$detail_rec->weight_unit,0,0,'C');
    $pdf->Cell(20,7,$detail_rec->measure.'  '.$detail_rec->measure_unit,0,0,'C');

    $pdf->Ln();
 
} 

$pdf->SetFont('Arial','',12);
$pdf->Line(7,190,208,190); //straight across
$pdf->Line(7,228,208,228); //straight across
$pdf->SetY(178);
$pdf->SetX(105);
$pdf->Cell(17,12,'Freight : ',0,0);
$pdf->Cell(24,12,$chgarr['FRT'],0,1,'L');

$pdf->Cell(90,12,'Country of Origin : USA',0,1);
$pdf->Cell(30,12,'Port of Loading : ',0,0);
$pdf->Cell(60,12,$rec->loading,0,1);
$pdf->Cell(30,15,'Containerized : Yes ___ No____ : ',0,1);

$pdf->Cell(30,10,'Destination : ',0,0);
$pdf->Cell(60,10,$rec->discharge,0,0);
$pdf->MultiCell( 109,6 , 'Please check goods for damages and/or loss before leaving warehouse. Any loss or damage must be inspected and noted by warehouse. We are not resposible for damages or loss reported after said goods leave the warehouse', 0,1);

//$pdf->Cell(160,8,'WHARFAGE AND HANDLING CHARGES NOT INCLUDED',0,1,'C');

//Arrival notice stamp
$pdf->SetY(190);
$pdf->SetX(105);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,7,'VESSEL: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,7,$rec->vessel_name,0,1);

$pdf->SetX(105);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,7,'BERTH: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,7,$rec->discharge,0,1);

$pdf->SetX(105);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,7,'MBL No: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,7,$rec->bill_of_lading_number,0,1);

$pdf->SetX(105);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,7,'HBL No: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,7,$rec->refnum,0,1);

$pdf->SetX(105);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,7,'REPORTED DATE: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$date = new DateTime($rec->arrival_date);
$pdf->Cell(94,7,$date->format('M d, Y'),0,1);
$pdf->SetFont('Arial','',14);


$pdf->Output();
?>
