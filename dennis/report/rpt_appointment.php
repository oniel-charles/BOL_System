<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';

/*
parm startDate/endDate/grouping/groupid
eg. 20180101/20180202/byuser/3
*/

class PDF extends FPDF
{

  // Simple table
function ItemGroupHeader()
{
    global $current_rec;
    $this->Ln();
    $this->SetFont('Arial','',9);
    $this->SetFillColor(220,220,220);
    $this->Cell(20,5,'Bill No.',0,0,'L',true);    
    $this->Cell(60,5,'Customer',0,0,'L',true);
    $this->Cell(20,5,'TRN',0,0,'L',true);
    $this->Cell(20,5,'UBA',0,0,'L',true);
    $this->Cell(20,5,'Passport',0,0,'L',true);
    $this->Cell(17,5,'Date',0,0,'C',true);
    $this->Cell(17,5,'Status',0,1,'C',true);

}
function ItemGroupFooter()
{
    global $current_rec;
    $this->SetFont('Arial','UB',14); 
    $this->Cell(50,7,' Total for'.$current_rec["description"],0,0);
    $this->Cell(50,7,' $'.$group_total,0,1);
    $this->SetFont('Arial','',12);    
}

function Header()
{
  global $company_rec;
  global $bill_rec;
  global $run_date;
  global $receipt_number;
  global $date_range;
  global $table_data;
  global $table_index;
  global $group_total;
  global $request;
  global $report_filter;

$this->SetFont('Arial','');  
$this->Cell( 30, 5,$run_date->format('d/m/Y'), 0,'L');
$this->SetFont('Arial','B');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,'Dennis Shipping (Ja) Ltd',0,0,'C');
$this->Cell(1,5,'',0,1,'C');
$this->SetFont('Arial','',12);
$this->Cell( 30, 5,$run_date->format('h:i a'), 0,'L');
$this->Cell( 150, 5,'', 0);
$this->Cell(1,5,'',0,1);
$this->SetFont('Arial','',12);
$this->Cell( 30, 5,'', 0,'L');	
$this->MultiCell( 150, 5, $company_rec->company_address, 0,'C');
$this->Ln();

$this->SetFont('Arial','UB');
if ($request[2]=='byuser'){
  $this->Cell( 200, 5,'Receipt Listing by Cashier '.$report_filter, 0,1,'C');
}  
if ($request[2]=='bydate'){
  $this->Cell( 200, 5,'Receipt Listing by Date '.$report_filter, 0,1,'C');
}  
$this->SetFont('Arial','');
$this->Cell(200, 5,$date_range, 0,1,'C');
$this->ItemGroupHeader();
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
         header("HTTP/1.1 401 Unauthorized");
          exit('invalid token');
        
  }
  
  //Get Company 
  $sql="SELECT  * from company limit 1";
 $company = mysqli_query($mysqli,$sql);
 $company_rec= mysqli_fetch_object($company);	

 $sql="SELECT data_value from system_values where code='freight_id'";
 $result = mysqli_query($mysqli,$sql);
 $freight= mysqli_fetch_object($result);	

   $order_number=0;
   $user=$claims['full_name'];

   $filter_str='';
   switch ($request[3]) {
    case 'missed':    
      $filter_str="  and (a.appointment_date <".$request[2]." and a.status='open') ";
    break;
    case 'reported':    
      $filter_str="  and  a.status='close' ";
    break;
  }   
  
 //$sql="select r.exchange_rate,d.currency_amount,u.user_name,r.payee,r.id,r.receipt_date,d.amount,c.description from (((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where  (r.cancelled is null or r.cancelled=0) and r.receipt_date between $request[0] and $request[1] $filter_str $order_by  ";
 $sql="SELECT a.trn,a.uba_code,a.passport_number, a.id,a.status,b.receipt_processed,b.bill_of_lading_number,b.consignee_name,a.appointment_date FROM appointment as a left join bill_of_lading as b on a.billoflading_id=b.id where a.appointment_date between $request[0] and $request[1]  $filter_str order by a.appointment_date desc";   
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

$run_date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$date_total=0;
$appt_date=0;
$table_index=0;
$g_total=0;
foreach($table_data as $current_rec){  
  if ( $current_rec["appointment_date"]!=$appt_date && $table_index>0){
    if ($date_total>0){
      $pdf->SetFont('Arial','B',8); 
      $date = new DateTime($appt_date);
      $pdf->Cell(154,5,"Total Appointments for ".$date->format('d/m/Y'),'T',0,'R');
      $pdf->Cell(20,5,$date_total,'T',1,'C');  
     
      $pdf->Ln();
      $date_total=0;
    }
  }
  
  $appt_date=$current_rec["appointment_date"];
  $pdf->SetFont('Arial','',8); 
  $table_index++;  
  $date_total++;
  $pdf->Cell(20,5,$current_rec["bill_of_lading_number"],0,0);
  $pdf->Cell(60,5,substr($current_rec["consignee_name"],0,30),0,0);
  $pdf->Cell(20,5,$current_rec["trn"],0,0);
  $pdf->Cell(20,5,$current_rec["uba_code"],0,0);
  $pdf->Cell(20,5,$current_rec["passport_number"],0,0);
  $status="Scheduled";
  if($current_rec["appointment_date"] <date('Ymd') && $current_rec["status"]=='open' ){ $status="Missed"; }
  if( $current_rec["status"]=='close' ){ $status="Attended"; }
  $date = new DateTime($current_rec["appointment_date"]);
  $pdf->Cell(17,5,$date->format('d/m/Y'),0,0,'C');
  $pdf->Cell(17,5,$status,0,1,'C');
  
  $g_total++;
}
    $date = new DateTime($current_rec["appointment_date"]);

    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(154,5,'Total Appointments for '.$date->format('d/m/Y'),'T',0,'R');
    $pdf->Cell(20,5,$date_total,'T',1,'C');   
    $pdf->SetFont('Arial','',8); 
    $pdf->Ln();
//  }

$pdf->Ln();
$pdf->SetFont('Arial','B',8); 
$pdf->Cell(154,5,'TOTAL APPOINTMENTS ','T',0,'R');
$pdf->Cell(20,5,$g_total,'T',1,'C');   
$pdf->SetFont('Arial','',8); 

$pdf->Output();

?>
