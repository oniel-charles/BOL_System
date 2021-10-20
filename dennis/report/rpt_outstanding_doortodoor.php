<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';

class PDF extends FPDF
{



function Header()
{
  global $company_rec;
  global $bill_rec;
  global $date;
  global $receipt_number;
  global $date_range;
  global $table_data;
  global $table_index;
  global $group_total;
  global $customer_type;

$this->SetFont('Arial','');  
$this->Cell( 30, 5,$date->format('d/m/Y'), 0,'L');
$this->SetFont('Arial','B');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,'Dennis Shipping (Ja) Ltd',0,0,'C');
$this->Cell(1,5,'',0,1,'C');
$this->SetFont('Arial','',12);
$this->Cell( 30, 5,$date->format('h:i a'), 0,'L');
$this->Cell( 150, 5,'', 0);
$this->Cell(1,5,'',0,1);
$this->SetFont('Arial','',12);
$this->Cell( 30, 5,'', 0,'L');	
$this->MultiCell( 150, 5, $company_rec->company_address, 0,'C');
$this->Ln();

$this->SetFont('Arial','UB');
$this->Cell( 200, 5,'Outstanding '.$customer_type, 0,1,'C');
$this->SetFont('Arial','');
$this->Cell( 200, 5,$date_range, 0,1,'C');
$this->Ln();
$this->SetFont('Arial','',10);
$this->SetFillColor(220,220,220);
$this->Cell(20,5,'Reference ',1,0,'C',true);
$this->Cell(60,5,'Consignee',1,0,'L',true);
$this->Cell(50,5,'Vessel',1,0,'L',true);
$this->Cell(20,5,'Arrival Date',1,0,'C',true);
$this->Cell(20,5,'Voyage',1,1,'C',true);

}

function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Text color in gray
    $this->SetTextColor(128);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}


}

date_default_timezone_set('America/Jamaica');
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$post_data = json_decode(file_get_contents('php://input'),true);
//echo $post_data->cus_id;
//echo $post_data->no_order;

//exit();
// connect to the mysql database
if (mysqli_connect_errno()){
  header("HTTP/1.1 401 Unauthorized");
  exit(mysqli_connect_error());
}
mysqli_set_charset($mysqli,'utf8'); 

$claims=authenticateToken();
if ($claims==null){    
    //     header("HTTP/1.1 401 Unauthorized");
     //     exit('invalid token');
        
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
  $item_str='';
  if (intval( $request[3])!=0){
       $item_str=' and d.charge_item_id='.intval( $request[3]);
  }
 $customer_type=ucwords($request[2]);
 $sql="SELECT b.customer_type,b.receipt_processed,b.order_processed,b.notify_name,b.notify_address,b.notify_phone_num,b.id,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.notify_date, v.stripped,v.stripped_date,s.vessel_name,v.voyage_number,v.arrival_date FROM (((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE  parent_bol<>1 and ( receipt_processed is null or receipt_processed =0) and v.arrival_date between $request[0] and $request[1] and b.customer_type='$request[2]'";
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$s_date= new DateTime($request[0]) ;
$e_date= new DateTime($request[1]) ;
$date_range =$s_date->format('d/m/Y').' to '.$e_date->format('d/m/Y');

$date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$total=0;
$desc='';
$table_index=0;
$g_total=0;
$pdf->SetFont('Arial','',8);
foreach($table_data as $current_rec){

  $desc=$current_rec["description"];
  $table_index++;
  $date = new DateTime($current_rec["arrival_date"]);
  $cancel_date = new DateTime($current_rec["cancel_date"]);
  $pdf->Cell(20,5,$current_rec["refnum"],0,0);
  $pdf->Cell(60,5,$current_rec["consignee_name"],0,0);
  $pdf->Cell(50,5,$current_rec["vessel_name"],0,0);  
  $pdf->Cell(20,5,$date->format('d/m/Y'),0,0,'C');
  $pdf->Cell(20,5,$current_rec["voyage_number"],0,1,'C');
  
}
$pdf->Output();

?>
