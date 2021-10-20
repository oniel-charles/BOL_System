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

$bl_ids = array();
/*if (isset($this->requestUrl[3])){
 
  $sql="SELECT b.id,m.master_bl, b.`bill_of_lading_number`,concat(consignee_fname,' ',consignee_sname) as consignee_name,`consignee_address`,`consignee_phone_num`,concat(shipper_fname,' ',shipper_sname) as shipper_name,`shipper_address`,`shipper_phone_num`,concat(notify_fname,' ',notify_sname) as notify_name,`notify_address`,`notify_phone_num` FROM `bill_of_lading` as b left join (select id,bill_of_lading_number as master_bl from bill_of_lading) as m on b.master_bol_id=m.id WHERE voyage_id=".$this->requestUrl[3]." and b.parent_bol=0 order by m.master_bl,b.id " ;
  $table_data = mysqli_query($mysqli,$sql);	
  foreach($table_data as $current_rec){
       array_push($bl_ids,$current_rec["id"]);
     }
}else{
  */
  array_push($bl_ids, $this->requestUrl[2]);  
//}

for($j=0;$j<count($bl_ids);$j++){
// excecute SQL statement
  $sql="SELECT v.id,v.document_number,l.shipping_line_name, b.value_of_goods,p.id as master_id,concat(p.consignee_fname,' ',p.consignee_sname,' ',p.consignee_other_name)  as master_name,consignee_phone_num,p.consignee_address as master_address,pl.port_name as loading,pd.port_name as discharge,v.contract_number,v.voyage_number,v.sail_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,concat(b.notify_fname,' ',b.notify_sname,' ',b.notify_other_name) as notify_name,b.notify_phone_num,b.notify_address,concat(b.consignee_fname,' ',b.consignee_sname,' ',b.consignee_other_name) as consignee_name,b.consignee_address,concat(b.shipper_fname,' ',b.shipper_sname,' ',b.shipper_other_name) as shipper_name,b.shipper_address,shipper_phone_num,s.vessel_name  FROM ((((((`bill_of_lading` as b left join booking as v on b.booking_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  as pd on v.port_of_discharge=pd.id) left join port  as pl on v.port_of_loading=pl.id) left join (select id,bill_of_lading_number,consignee_other_name,consignee_fname,consignee_sname,consignee_address from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) left join shipping_line as l on v.shipping_line_id=l.id) WHERE b.id=".$bl_ids[$j];
 $result = mysqli_query($mysqli,$sql);
  if (!$result) {
    http_response_code(404);
    die($sql.'<br>'.mysqli_error($mysqli));
  }
  $rec= mysqli_fetch_object($result);	
 

//Get detail
$detsql="SELECT measure,measure_unit,b.Description_of_goods,package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$bl_ids[$j];
$details = mysqli_query($mysqli,$detsql);
if (!$details) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
 }

 $sql="SELECT b.booking_id as id,sum(`number_of_items`) as number_of_items,sum(weight) as weight , sum(measure) as measure FROM `bill_of_lading_detail` d left join bill_of_lading as b on d.billoflading_id=b.id WHERE b.parent_bol=0 and b.booking_id=".$rec->id." group by b.booking_id  ";
 $result = mysqli_query($mysqli,$sql);
 $summary= mysqli_fetch_object($result);	 	

//$detail_rec= mysqli_fetch_object($detail);	


//charges
$chgarr = array();
$chgarr['FRT']=0;
$sql="SELECT b.amount,c.item_code,c.description,b.charge_item_id FROM bill_of_lading_other_charge as b left join charge_item as c on b.`charge_item_id`=c.id where billoflading_id=".$bl_ids[$j];
$result = mysqli_query($mysqli,$sql);

for ($i=0;$i<mysqli_num_rows($result);$i++) {
    $chg= mysqli_fetch_object($result);	  
    $chgarr[$chg->item_code] = $chg->amount; 
    if (strripos(strtolower($chg->description), 'freight')) {
       $chgarr['FRT']=$chg->amount; 
    }  
}

  // get container
  $sql="SELECT b.*,c.size_type_code,c.description FROM `booking_container` as b left join container_size_type as c on b.`container_size_type_id`=c.id where b.booking_id=".$rec->id." limit 1";
  $con = mysqli_query($mysqli,$sql);
  $con_rec= mysqli_fetch_object($con);	

//echo $f->format(1432);

$pdf->AddPage();
$pdf->SetFont('Times','B',16);
$pdf->Cell(40,5,'',0,0);
$pdf->SetTextColor(115,115,115);
$pdf->Cell(100,5,trim($rec->shipping_line_name),0,0,'C');
$pdf->SetFont('Times','B',10);
$pdf->Cell(40,5,'Bill of Lading',0,1,'C');
$pdf->SetTextColor(0,0,0);
$pdf->Ln();
$pdf->SetFont('Courier','',5);
$pdf->Cell(70,3,'SHIPPER/EXOPRTER REMITENTE',0,0,'L');
$pdf->Cell(25,3,'SHIPPER NUMBER',0,0,'C');
$pdf->Cell(60,3,'BOOKING NUMBER',0,0);
$pdf->Cell(30,3,'BROKERAGE',0,1,'R');
$y=$pdf->GetY();
$pdf->SetFont('Times','',10);
$pdf->MultiCell(74,4,trim($rec->shipper_name),0,1);
$pdf->MultiCell( 74, 4, $rec->shipper_address, 0,1);
$pdf->Cell(30,4,$rec->shipper_phone_num,0,0,'L');
$pdf->SetY($y);
$pdf->Cell(95,4,'',0,0,'L');
$pdf->Cell(55,4,$rec->document_number,0,1,'L');
$pdf->SetFont('Courier','',5);
$pdf->Cell(95,3,'',0,0,'L');
$pdf->Cell(55,3,'EXPORT REFERENCES / REFERENCIAS EXPORTACION',0,1,'L');
$pdf->Cell(1,7,'',0,1,'L');

$pdf->Line(83,20,83,30); //vertical
$pdf->Line(183,20,183,30);//vertical
$pdf->Line(83,30,208,30);
$pdf->Line(7,37,208,37); 

$pdf->SetFont('Courier','',5);
$pdf->Cell(70,3,'CONSIGNEE / CONSIGNADOA',0,0,'L');
$pdf->Cell(25,3,'',0,0,'C');
$pdf->Cell(60,3,'FORWARDING AGENT / AGENTE EMBARCADOR',0,0);
$pdf->Cell(30,3,'FMC NUMBER',0,1,'R');
$y=$pdf->GetY();
$pdf->SetFont('Times','',10);
$pdf->MultiCell(74,4,trim($rec->consignee_name),0,1);
$pdf->MultiCell( 74, 4, $rec->consignee_address, 0,1);
$pdf->Cell(30,4,$rec->consignee_phone_num,0,0,'L');
$pdf->SetY($y);
$pdf->Cell(95,4,'',0,0,'L');
$pdf->MultiCell(74,4,trim($rec->shipper_name),0,1);
$pdf->Cell(95,4,'',0,0,'L');
$pdf->MultiCell( 74, 4, $rec->shipper_address, 0,1);
$pdf->Cell(95,4,'',0,0,'L');
$pdf->Cell(30,4,$rec->shipper_phone_num,0,0,'L');

$pdf->Line(83,37,83,46); //vertical
$pdf->Line(183,37,183,46);//vertical
$pdf->Line(83,46,105,46);
$pdf->Line(183,46,208,46);
$pdf->Line(105,53,208,53);
$pdf->Line(7,65,208,65); 

$pdf->SetXY(106,55);
$pdf->SetFont('Courier','',5);
$pdf->Cell(55,3,'POINT AND COUNTRY OF ORIGIN / LUGAR Y PAIS DE OHIGEN',0,1,'L');
$pdf->Cell(1,7,'',0,1,'L');
$pdf->Cell(95,3,'NOTIFY PARTY/ DIRIGIR NOTIFICACION DE LLEGADA ',0,0,'L');
$pdf->Cell(60,3,'DOMESTIC ROUTING EXPORT INSTRUCTIONS/ RUTA DOMESTICA /  ',0,1,'L');
$y=$pdf->GetY();
$pdf->SetFont('Times','',10);
$pdf->MultiCell(74,4,$rec->notify_name,0,1);
$pdf->MultiCell( 74, 4, $rec->notify_address, 0,1);
$pdf->Cell(30,4,$rec->notify_phone_num,0,0,'L');
$pdf->SetXY(106,70);
$pdf->SetFont('Times','B',10);
$pdf->MultiCell(40,4,'FREIGHT PREPAID EXPRESS RELEASE SC# '.$rec->contract_number,0,1);
$pdf->SetY(88);
$pdf->SetFont('Courier','',5);
$pdf->Cell(55,4,'PLACE OF RECEIPT/ CARGA RECIBIDA EN',0,1,'L');
$pdf->Cell(50,4,'VESSEL NAME/  VAPOR     VOY NO / VIAJE NO ',0,0,'L');
$pdf->Cell(47,4,'PT OF LOADING / PUERTO DE CARGA ',0,0,'L');
$pdf->Cell(95,4,'LOADING PIER TERMINAL / TERMINAL DE EMBARQUE',0,0,'L');
$pdf->SetY(94); //$pdf->Cell(1,3,'',0,1,'L');
$pdf->SetFont('Times','',10);
$pdf->Cell(30,5,$rec->vessel_name,0,0,'L');
$pdf->Cell(15,5,$rec->voyage_number,0,0);
$pdf->Cell(60,5,$rec->loading,0,1,'C');
$pdf->SetFont('Courier','',5);
$pdf->Cell(97,3,'PORT OF DISCHARGE / PUERTO DE DESCARGA',0,0,'L');
$pdf->Cell(50,3,'YPE OF MOVE / TIPO DE MOVIMIENTO ',0,1,'L');
$pdf->SetFont('Times','B',8);
$pdf->Cell(45,3,$rec->discharge,0,1,'C');
$pdf->Cell(190,4,'PARTICULARS FURNISHED BY SHIPPER',0,1,'C');
$pdf->Line(83,65,83,74); //vertical
$pdf->Line(83,74,105,74);
$pdf->Line(7,89,105,89);  //straight across
$pdf->Line(7,93,208,93);  //straight across
$pdf->Line(56,93,56,105); //vertical
$pdf->Line(7,99,208,99);  //straight across
$pdf->Line(7,105,208,105);  //straight across
$pdf->Line(7,109,208,109);  //straight across
$pdf->Line(105,20,105,105); //vertical

$pdf->SetFont('Times','B',7);
$pdf->Cell(40,4,'MARKS AND NUMBERS ',0,0,'L');
$pdf->Cell(23,4,'NO. OF PKGS',0,0,'L');
$pdf->Cell(77,4,'DESCRIPTION OF PACKAGES AND GOODS',0,0,'L');
$pdf->Cell(30,4,'GROSS WEIGTH',0,0,'L');
$pdf->Cell(30,4,'MEASUREMENT',0,1,'L');
$pdf->Cell(40,4,'MARCAS Y NUMEROS ',0,0,'L');
$pdf->Cell(23,4,'NO DE  BUIJOS',0,0,'L');
$pdf->Cell(77,4,'CONTENIDO SEGUN EMBARCADOR',0,0,'L');
$pdf->Cell(30,4,'LBS/ LIBRA/KILOS',0,0,'L');
$pdf->Cell(30,4,'MEDIDAS',0,1,'L');

$pdf->Line(7,117,208,117);  //straight across
$pdf->Line(73,109,73,170); // Left of number of pieces vertical
$pdf->Line(50,109,50,170); // Left of number of pieces vertical
$pdf->Line(150,109,150,170); // Left of number of pieces vertical
$pdf->Line(180,109,180,170); // Left of number of pieces vertical

//********************************************************************** */


$pdf->Line(7,20,7,170); //vertical side
$pdf->Line(208,20,208,170); //vertical side


$pdf->Line(7,20,208,20); //top


//$pdf->Line(7,83,208,83);  //straight across





$max_y=$pdf->GetY();

//write details
$detail_desc="";
$total_weight=0;
$total_measure=0;
$pdf->SetFont('Times','',9);
for ($i=0;$i<mysqli_num_rows($details);$i++) {    
    $detail_rec= mysqli_fetch_object($details);
    $pdf->SetFont('Times','B',8);
    $pdf->Cell(33,5,'CONTAINER ',0,0);
    $pdf->SetFont('Times','',9);
    $pdf->Cell(30,5, $detail_rec->number_of_items, 0,0,'C');
    $pdf->Cell(77,5,'1-'.strtoupper($con_rec->description),0,0);
    $pdf->Cell(30,5,$summary->weight.' LBS',0,0,'R');
    $pdf->Cell(28,5,$summary->measure,0,1,'R');
    
    $pdf->Cell(33,5,$con_rec->container_number,0,1);
    $pdf->Cell(63,5,'',0,0,'C');
    
    $pdf->Cell(77,5, $summary->number_of_items.' PIECES', 0,0);
    
    $pdf->Cell(30,5,number_format($summary->weight/2.2,2).' KG',0,0,'R');
    $pdf->Ln();    
    $pdf->Ln();    
    $y=$pdf->GetY();
    $pdf->SetFont('Times','B',8);
    $pdf->Cell(33,5,'SEAL# ',0,1);
    $pdf->SetFont('Times','',9);
    $pdf->Cell(33,10,$con_rec->container_seal,0,1);
    $pdf->SetXY(73,$y);
    $pdf->MultiCell( 54, 5, strtoupper($detail_rec->Description_of_goods), 0,1);
    $pdf->Ln();    
    $pdf->SetX(73);
    $pdf->Cell(33,6,'HS 210690',0,1);
} 
$pdf->Ln();       
$pdf->Line(7,170,208,170); //bottom line
$pdf->SetY(169);
$pdf->SetFont('Times','B',9);
$pdf->Cell(200,6,'Declared Value per Package if Value More Than $500.00 per Package U.S.',0,1,'C');
$pdf->Cell(90,5,'FREIGHT CHARGES PAYABLE   AT: ','LTB',0);
$pdf->Cell(50,5,'BY: ','TB',0);
$pdf->Cell(40,5,'TARIFF NO. ITEM NO.',1,0);
$pdf->Cell(20,5,'',1,0);
$pdf->Cell(1,7,'',0,1);

$pdf->SetFont('Times','B',9);
$pdf->Cell(57,5,'OCEAN FREIGHT CHARGED ON ',0,0,'C');
$pdf->Cell(23,5,'PREPAID',0,0);
$pdf->Cell(23,5,'COLLECT',0,0);
$pdf->SetFont('Times','B',6);
$pdf->MultiCell( 85, 3,'RECEIVED the described goods or packages or containers sold to contain goods in apparent good order and condition unless otherwise indicated.  To be transported and delivered or transshipped as herein provided. THE RECEIPT CUSTODY, CARRIAGE, DELIVERY, AND TRANSHIPPING OF THE GOODS ARE SUBJECT TO THE TERMS APPEARING IN THE FACE AND BACK HEREOF.  IN WITNESS WHEREOF THE CARRIER BY IT’S AGENY HAS SIGNED 3 BILLS OF LADING ALL OF THE SAME TENOR AND DATE.  ONE OF WHICH BEING ACCOMPLISHED, THE OTHERS TO STAND VOID.

CARRIER: SEABOARD MARINE, LTD.
', 0,1);
$pdf->SetY(200);
$pdf->SetFont('Times','B',10);
$pdf->Cell(80,5,'EXPRESS RELEASEFREIGHT PREPAID',0,0);
$pdf->SetY(253);
$pdf->SetFont('Times','B',10);
$pdf->Cell(80,5,'TOTAL CHARGES',0,0);

$pdf->SetFont('Times','',6);
$pdf->SetXY(114,214);
$pdf->Cell(70,5,'By:','B',0);
$pdf->SetXY(114,225);
$pdf->Cell(25,5,'B/L NO.','T',0);
$pdf->Cell(15,5,'MO','T',0);
$pdf->Cell(15,5,'DAY','T',0);
$pdf->Cell(15,5,'YEAR','T',0);

$pdf->SetFont('Times','',9);
$pdf->Line(7,182,110,182); // h
$pdf->Line(7,190,110,190); // h
$pdf->Line(7,253,110,253); // h
$pdf->Line(7,260,110,260); // h
$pdf->Line(7,182,7,260); // v
$pdf->Line(67,182,67,260); // v
$pdf->Line(90,182,90,260); // v
$pdf->Line(110,182,110,260); // v


//$pdf->Cell(160,8,'WHARFAGE AND HANDLING CHARGES NOT INCLUDED',0,1,'C');

//$pdf->SetY(213);
}
$pdf->Output();
?>
