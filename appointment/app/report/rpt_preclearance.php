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
  $sql="SELECT p.id as master_id,p.consignee_name as master_name,p.consignee_address as master_address,pl.port_name as loading,pd.port_name as discharge,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.notify_name,b.notify_address,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM (((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  as pd on b.port_of_discharge=pd.id) left join port  as pl on b.port_of_loading=pl.id) left join (select id,bill_of_lading_number,consignee_name,consignee_address from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$this->requestUrl[2];
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
$charges = mysqli_query($mysqli,$sql);
/*
for ($i=0;$i<mysqli_num_rows($charges);$i++) {
    $chg= mysqli_fetch_object($charges);	  
    $chgarr[$chg->item_code] = $chg->amount; 
    if (stripos($chg->description, 'freight')) {
       $chgarr['FRT']=$chg->amount; 
    }  
}
*/
  // get container
  $sql="SELECT * FROM `bill_of_lading_container` WHERE `billoflading_id`=".$rec->master_id." limit 1";
  $con = mysqli_query($mysqli,$sql);
  $con_rec= mysqli_fetch_object($con);	

  // GET RECEIPT NUMBER
  $sql="SELECT id FROM `receipt` WHERE BILLOFLADING_ID="+$this->requestUrl[2]+" AND `cancelled`=0 ";
  $rc = mysqli_query($mysqli,$sql);
  $rc_rec= mysqli_fetch_object($rc);	

  

  $date = new DateTime('NOW');  
//echo $f->format(1432);

$pdf = new FPDF();
//$pdf = new FPDF('L','mm',array(200,300));
$pdf = new FPDF('P','mm','Letter');
$pdf->AddPage();
$pdf->SetFont('Times','B',18);
$pdf->Cell(200,5,'PRE-CLEARANCE CUSTOMER SHEET',0,1,'C');
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,12,'CUSTOMER NAME: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,12,$rec->consignee_name,0,1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,12,'DATE: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,12,$date->format('M d, Y'),0,1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,12,'INVOICE#: ',0,0,'L');
$pdf->Cell(94,12,'',0,1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,12,'REC#: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,12,$rc_rec->id,0,1);

$pdf->SetFont('Arial','B',12);
$detail_rec= mysqli_fetch_object($details);
$pdf->Cell(42,12,'DESCRIPTION: ',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->Cell(94,12,$detail_rec->Description_of_goods,0,1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(42,12,'PAYMENT METHOD: ',0,0,'L');
$pdf->Cell(94,12,'',0,1);

$pdf->Ln();

$pdf->Cell(95,10,'EXPENSE',0,0,'C');
$pdf->Cell(105,10,'INCOME',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(90,5,'$',0,0,'R');
$pdf->Cell(100,5,'$',0,1,'R');



$pdf->SetFont('Arial','',10);

$pdf->Line(7,105,7,200); //vertical side
$pdf->Line(208,105,208,200); //vertical side

$pdf->Line(7,105,208,105); //top
$pdf->Line(110,105,110,200); //vertical

$pdf->Line(7,125,208,125);  //straight across

$pdf->Line(85,125,85,200); // vertical charges
$pdf->Line(185,125,185,200); // vertical charges
$pdf->Line(75,105,208,105); // horizontal
$pdf->Ln();

$y=$pdf->GetY();	
$pdf->SetFont('Arial','',10);

$income=0;
mysql_data_seek($charges, 0);
for ($i=0;$i<mysqli_num_rows($charges);$i++) {
  $pdf->SetX(110);  
  $chg= mysqli_fetch_object($charges);	 
  $chgarr[$chg->item_code] = $chg->amount; 
  if ($chg->item_code !='KWL' && $chg->item_code !='CUSTOMS'){
      $pdf->Cell(75,7,$chg->description,0,0,'C');
      $pdf->Cell(25,7,$chg->amount,0,1,'L');  
      $income += $chg->amount; 
  }
}
$expense =$chgarr['KWL']+$chgarr['CUSTOMS'];
$pdf->SetY($y);  
$pdf->Cell(75,5,'KWL  ',0,0,'C');
$pdf->Cell(25,5,$chgarr['KWL'],0,1,'L');
$pdf->Ln();
$pdf->Cell(75,5,'CUSTOMS  ',0,0,'C');
$pdf->Cell(25,5,$chgarr['CUSTOMS'],0,1,'L');

$pdf->Line(7,190,208,190); //straight across
$pdf->Line(7,200,208,200); //straight across
$pdf->SetY(190);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(75,5,'TOTAL  ',0,0,'C');
$pdf->Cell(25,5,$expense,0,0,'L');
$pdf->Cell(75,5,'TOTAL  ',0,0,'C');
$pdf->Cell(25,5,$income,0,0,'L');

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(40,10,'CASHIER :  ',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,$this->claims['user'],'B',1,'C');
$pdf->SetFont('Arial','B',10);
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(40,10,'APPROVED BY :  ',0,0);
$pdf->Cell(50,5,'','B',0,'C');


$pdf->Output();
?>
