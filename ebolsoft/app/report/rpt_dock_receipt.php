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

      
$pdf = new FPDF();
//$pdf = new FPDF('L','mm',array(200,300));
$pdf = new FPDF('P','mm','Letter');



// excecute SQL statement
  $sql="SELECT b.*,s.shipping_line_name,v.vessel_name,l.port_name as port_load,d.port_name as port_discharge ,cd.country_name FROM (((((booking as b left join vessel as v on b.vessel_id=v.id) left join port as l on b.port_of_loading=l.id) left join port as d on b.port_of_discharge=d.id) left join country as cd on d.country_id=cd.id) left join shipping_line as s on b.shipping_line_id=s.id) where b.id=".$this->requestUrl[2];
  $result = mysqli_query($mysqli,$sql);
  $rec= mysqli_fetch_object($result);	

// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

//Get detail
//$detsql="SELECT b.*,c.size_type_code,c.description FROM `booking_container` as b left join container_size_type as c on b.`container_size_type_id`=c.id where b.booking_id=".$this->requestUrl[2];
$detsql="SELECT b.*,c.size_type_code,c.description,d.* FROM ((`booking_container` as b left join container_size_type as c on b.`container_size_type_id`=c.id) left join (select h.booking_id,sum(x.number_of_items) as number_of_items,sum(x.measure) as measure,sum(x.weight) as weight from bill_of_lading as h  left join bill_of_lading_detail as x  on h.id=x.billoflading_id where h.parent_bol=0 and h.booking_id=".$this->requestUrl[2]." group by h.booking_id) as d on b.booking_id=d.booking_id  )  where  b.booking_id=".$this->requestUrl[2];
$details = mysqli_query($mysqli,$detsql);
if (!$details) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
 }


//echo $f->format(1432);

$pdf->AddPage();
$pdf->SetLeftMargin(7);   
$pdf->SetFont('Times','B',22);
$pdf->Cell(200,5,'DOCK RECEIPT',0,1,'C');
$pdf->SetFont('Times','',14);
$pdf->Cell(200,5,$rec->shipping_line_name,0,1,'C');
//$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(91,5,'Shipper/Exporter:',0,0,'L');
$pdf->Cell(30,5,'Document No.:',0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,$rec->document_number,0,0);
$pdf->Cell(1,7,'',0,1);
$y=$pdf->GetY();
$pdf->Cell(91,5,'',0,0);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(50,5,'EXPORT REFERENCES / REFERENCIAS EXPORTACION',0,1);
$pdf->SetFont('Arial','',10);
$pdf->SetY($y-2);
$pdf->MultiCell( 98, 5, $company_rec->company_name, 0,1);
$pdf->MultiCell( 98, 5, $company_rec->company_address, 0,1);
$pdf->Cell(71,5,'TEL:'.$company_rec->phone.'    FAX:'.$company_rec->fax,0,0);
$pdf->SetY(47);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(91,5,'CONSIGNEE / CONSIGNADOA',0,0);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(88,5,'Forwarding Agent References ',0,1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(98);
$pdf->MultiCell( 95, 5, $company_rec->company_name, 0,1);
$pdf->SetX(98);
$pdf->MultiCell( 95, 5, $company_rec->company_address, 0,1);
$pdf->SetX(98);
$pdf->Cell(68,5,'TEL:'.$company_rec->phone.'    FAX:'.$company_rec->fax,0,1);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(91,5,'NOTIFY PARTY / DIRIGIR NOTIFICACION DE LLEGADA',0,0);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(88,7,'Port and Country of Origin: ',0,1);


$pdf->SetX(98);
$pdf->Cell(60,7,'Domestic Routing/Export Instruction ',0,1);
$pdf->SetFont('Arial','',10);
$pdf->SetY(90);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(91,5,'PLACE OF RECEIPT / CARGA RECIBIDA EN ',0,0);
$pdf->Cell(88,5,'PLACE OF RECEIPT / CARGA RECIBIDA EN ',0,1);
$pdf->Ln();
$pdf->Cell(91,5,'VESSEL NAME/VAPOR     VOY NO/VIAJE NO     PT OF LOADING/ PUERTO ',0,0);
$pdf->Cell(88,5,'LOADING PIER TERMINAL / TERMINAL DE EMBARQUE ',0,1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(40,5,$rec->vessel_name,0,0);
$pdf->Cell(15,5,$rec->voyage_number,0,0,'L');
$pdf->Cell(30,5,$rec->port_load,0,1);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(91,6,'PORT OF DISCHARGE / PUERTO DE DESCARGA ',0,0);
$pdf->Cell(88,6,'TYPE OF MOVE / TIPO DE MOVIMIENTO ',0,1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(55,5,$rec->port_discharge,0,0);
$pdf->Cell(40,5,$rec->country_name,0,1);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(180,5,'PARTICULARS FURNISHED BY SHIPPER ',0,1,'C');

$pdf->Cell(33,5,'MARKS AND ',0,0);
$pdf->Cell(22,5,'NO. OF ',0,0);
$pdf->Cell(85,5,'DESCRIPTION OF PACKAGES AND GOODS ',0,0);
$pdf->Cell(30,5,'GROSS ',0,0);
$pdf->Cell(30,5,'MEASUREMENT ',0,1);

$pdf->Cell(33,5,'NUMBERS ',0,0);
$pdf->Cell(22,5,'PACKAGES',0,0);
$pdf->Cell(85,5,' ',0,0);
$pdf->Cell(30,5,'WEIGHT ',0,1);

$pdf->SetY(135);

$pdf->Line(7,25,7,190); //vertical side
$pdf->Line(208,25,208,190); //vertical side
//$pdf->Line(7,265,208,265); //bottom line

$pdf->Line(7,25,208,25); //top
$pdf->Line(98,32,208,32); //horizontal under doc#
$pdf->Line(98,37,208,37); //horizontal under doc#2
$pdf->Line(98,73,208,73); //horizontal under port of origin
$pdf->Line(7,47,208,47); //horizontal
$pdf->Line(7,67,208,67); //horizontal under forwarding
$pdf->Line(7,100,208,100); //horizontal 
$pdf->Line(7,111,208,111); //horizontal 
$pdf->Line(7,121,208,121); //horizontal  Port discharge
$pdf->Line(7,125,208,125); //horizontal 
$pdf->Line(98,25,98,121); //vertical

$pdf->Line(7,90,208,90);  //straight across
$pdf->Line(7,135,208,135);  //straight across

$pdf->Line(62,125,62,190); // Left of number of pieces vertical
$pdf->Line(40,125,40,190); // Left of number of pieces vertical
$pdf->Line(147,125,147,190); // Left of number of pieces vertical
$pdf->Line(177,125,177,190); // Left of number of pieces vertical

$pdf->Line(7,190,208,190); //straight across
//$pdf->Line(7,228,208,228); //straight across

//$pdf->Line(76,100,208,100); // horizontal

//write details
$detail_desc="";
$total_weight=0;
$total_measure=0;

for ($i=0;$i<mysqli_num_rows($details);$i++) {
/*
$pdf->Cell(33,5,'MARKS AND ',0,0);
$pdf->Cell(22,5,'NO. OF ',0,0);
$pdf->Cell(85,5,'DESCRIPTION OF PACKAGES AND GOODS ',0,0);
$pdf->Cell(30,5,'GROSS ',0,0);
$pdf->Cell(30,5,'MEASUREMENT ',0,1);
*/  
    $detail_rec= mysqli_fetch_object($details);
    $pdf->Cell(33,5,'CONTAINER# ',0,1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(33,5,$detail_rec->container_number,0,0);
    $pdf->Cell(22,5,'1',0,0,'C');
    $pdf->Cell(85,5,'1-'.$detail_rec->description,0,1);
    //
    $pdf->Cell(55,5,'',0,0);
    $pdf->Cell(85,5,$detail_rec->number_of_items.' PIECES',0,0);
    $pdf->Cell(30,5,number_format($detail_rec->weight,2).'lb',0,0,'R');
    $pdf->Cell(30,5,number_format($detail_rec->measure,2).'cf',0,1,'R');

    $pdf->Cell(140,5,'',0,0);
    $pdf->Cell(30,5,number_format($detail_rec->weight/2.205,2).'kg',0,0,'R');
    $pdf->Cell(30,5,number_format($detail_rec->measure/35.315,2).'cm',0,0,'R');
    //
    $pdf->Ln();    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(33,5,'SEAL# ',0,1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(33,10,$detail_rec->container_seal,0,1);
    
 
} 

$pdf->SetFont('Arial','B',10);

$pdf->SetY(190);
$pdf->Cell(90,7,'DELIVERED BY',0,1);
$pdf->Ln();
$pdf->Cell(30,12,'TARE: '.$rec->tare,0,1);
$pdf->SetXY(90,190);
$pdf->MultiCell( 109,6 , "RECEIVED THE ABOVE DESCRIBED GOODS OR PACKAGES SUBJECT TO ALL THE TERMS OF THE UNDERSIGNED'S REGULAR FORM OF DOC AND BILL OF LADING WHICH SHALL CONSTITUTE THE CONTRACT UNDER WHICH ARE AVAILABLE FROM THE CARRIER ON REQUEST AND MAY BE INSPECTED AT ANY OF IT'S OFFICES", 0,1);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(50,7,'Lighter Truck : BY ORDER OF SHIPPER',0,1);

$pdf->Cell(30,7,'Arrived  Date :              Time : ',0,1);
$pdf->Cell(30,7,'Uploaded Date :              Time : ',0,1);
$y=$pdf->GetY();

//$pdf->Cell(160,8,'WHARFAGE AND HANDLING CHARGES NOT INCLUDED',0,1,'C');

//$pdf->SetY(213);

$pdf->Output();
?>
