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
         header("HTTP/1.1 401 Unauthorized");
          exit('invalid token');
          
  }

    //Get Company 
    $sql="SELECT  * from company limit 1";
    $company = mysqli_query($mysqli,$sql);
    $company_rec= mysqli_fetch_object($company);	

  // excecute SQL statement
  $sql="SELECT port.port_name,v.id as voyage_id,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name,b.consignee_phone_num,b.notify_phone_num  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".$request[0];
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
$voy_date = new DateTime($rec->arrival_date);

//echo $f->format(1432);

//Updates
$order_number=0;
$reprint='';
   $user=$claims['full_name'];
   $sql="SELECT s.order_date,s.id,u.user_name,u.full_name FROM `delivery_order` as s left join user_profile as u on s.created_by=u.id where (cancelled is null or cancelled=0) and billoflading_id=".$request[0];
   $order = mysqli_query($mysqli,$sql);
   if (mysqli_num_rows( $order) >0){
       $order_rec= mysqli_fetch_object($order);	
       $order_number=$order_rec->id;
       $order_date= new DateTime($order_rec->order_date);
       $reprint='REPRINT';
       $user=$order_rec->full_name;
   }

   if($order_number==0){
    $order_date= new DateTime('NOW');  
    $user =$claims['id'];
    date_default_timezone_set('America/Jamaica');
    $date= date('Ymd') ;
    $sql = "INSERT INTO `delivery_order` (`voyage_id`, `billoflading_id`, `created_by`,  `cancelled`, `order_date`, `cancel_by`, `cancel_date`)   VALUES ($rec->voyage_id, $request[0], $user, 0, $date,'',0)";
    $result = mysqli_query($mysqli,$sql);
    $order_number= mysqli_insert_id($mysqli);
    if (!$result) {
      http_response_code(404);
      die($sql.'<br>'.mysqli_error($mysqli));
    }
    $sql=" update bill_of_lading set order_processed=1 where id in (".$request[0].",".intval($rec->master_id).")";
    $result = mysqli_query($mysqli,$sql);
    if (!$result) {
      http_response_code(404);
      die($sql.'<br>'.mysqli_error($mysqli));
    }
  }
//*********** */
$pdf = new FPDF();
//$pdf = new FPDF('L','mm',array(200,300));

$pdf = new FPDF('P','mm',array(177.8,177.8));
//$pdf = new FPDF('P','mm',array(215.9,177.8));

$pdf->AddPage();
$pdf->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$pdf->SetFont('ManuskriptGothisch','',26);
$pdf->Image('../../img/Company_icon.png',10,10,-300);
$pdf->Cell(30,10,'',0,0); $pdf->SetTextColor(0,0,255);
$pdf->Cell(65,10,'Cargo Shipping',0,0);
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
$pdf->MultiCell( 75, 5, $company_rec->company_address, 0,'L');

$pdf->SetY($y); //set pointer back to previous values
$pdf->SetX($x);
$pdf->Cell(55,5,'Date',0,0,'R');
$pdf->Cell(45,5,$form_date->format('d/m/Y'),0,1,'L');  
$pdf->SetTextColor(255,0,0);
$pdf->Cell(130,5,$reprint,0,0,'R');
$pdf->SetTextColor(0,0,0);
$pdf->Ln(1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(120,15,'DELIVERY SLIP',0,1,'C');
$pdf->SetFont('Arial','',10);


$pdf->Cell(40,5,'CONSIGNEE\'S NAME ',0,0);
$pdf->Cell(110,5,$rec->consignee_name,'B',1);

$pdf->Ln(1);

$pdf->Cell(10,5,'DR# ',0,0);
$pdf->Cell(80,5,$rec->refnum,'B',0);
//$pdf->Cell(60,5,'TEL# ________________________________________',0,1);
$pdf->Cell(6,5,'Tel ',0,0);
$pdf->Cell(50,5,$rec->consignee_phone_num ,'B',0);
$pdf->Ln(8);
$pdf->Cell(15,5,'Vessel ',0,0);
$pdf->Cell(80,5,$rec->vessel_name.'   -  '.$voy_date->format('d/m/Y'),'B',0);
$pdf->Cell(24,5,'Manifest# ',0,0);
$pdf->Cell(30,5,$rec->voyage_number,'B',1);
$pdf->Ln(1);
$pdf->Cell(55,5,'SEAL# ________________________________________',0,1);
$pdf->Ln(1);

$pdf->Cell(40,5,'DELIVERY ADDRESS ',0,0);
$pdf->SetFont('Arial','U',10);
$pdf->MultiCell( 120, 5,$rec->consignee_address , 0,1);
$pdf->SetFont('Arial','',10);
//$pdf->Cell(138,5,$rec->consignee_address,'B',1);
//$pdf->Cell(178,10,'','B',1);
$pdf->Ln(1);

$pdf->Cell(55,5,'NO. OF PIECES DELIVERED ',0,0);
$pdf->Cell(40,5,$detail_rec->number_of_items,'B',0);
$pdf->Cell(15,5,'ID/TRN',0,0);
$pdf->Cell(40,5,'','B',1);
$pdf->Ln(1);

$pdf->Cell(30,5,'DELIVERY TIME ',0,0);
$pdf->Cell(20,5,'','B',0);
$pdf->Cell(15,5,'am/pm',0,1);
$pdf->Ln(1);

$pdf->Cell(30,5,'DELIVERY DATE ',0,0);
$pdf->Cell(30,5,'','B',0);
$pdf->Cell(15,5,'MONTH',0,0);
$pdf->Cell(40,5,'','B',0);
$pdf->Cell(15,5,'YEAR 20',0,0);
$pdf->Cell(10,5,'','B',1);
$pdf->Ln(1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(150,10,'CUSTOMERS ONLY',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Ln(2);
$pdf->Cell(150,5,'','LTR',1);
$pdf->Cell(70,5,'Shipment was checked by customers:  Yes','L',0);
$pdf->Cell(5,5,'',1,0);
$pdf->Cell(8,5,'No',0,0);
$pdf->Cell(5,5,'',1,0);
$pdf->Cell(62,5,'','R',1);
//$pdf->Ln(1);


$pdf->Cell(70,5,'Are all goods accounted for?                Yes','L',0);
$pdf->Cell(5,5,'',1,0);
$pdf->Cell(8,5,'No',0,0);
$pdf->Cell(5,5,'',1,0);
$pdf->Cell(62,5,'','R',1);


$pdf->Cell(150,5,'If no please state __________________________________________________________','LR',1);


$pdf->Cell(100,5,'Was the shipment delivered in good condition?                Yes','L',0);
$pdf->Cell(5,5,'',1,0);
$pdf->Cell(8,5,'No',0,0);
$pdf->Cell(5,5,'',1,0);
$pdf->Cell(32,5,'','R',1);

$pdf->Cell(150,5,'Received by _______________________________________________________','LR',1);


$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(3,5,'I','L',0);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(65,5,'_______________________________________',0,0);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(82,5,'take full responsibilty for shipment received and  ','R',1);
$pdf->Cell(150,5,'Cargo Shipping Company Ltd is not responsible for any theft or damage.','LR',1);

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(150,5,'__________________________','LR',1);
$pdf->Cell(150,5,'Customer\'s Signature','BLR',1);
$pdf->Ln(4);
$pdf->Cell(30,5,'',0,0);
$pdf->Cell(80,5,'Delivery Person ______________________________________',0,1);



$pdf->Output();

?>
