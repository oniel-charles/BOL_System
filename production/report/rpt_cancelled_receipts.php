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

$this->SetFont('Arial','');  
$this->Cell( 30, 5,$date->format('d/m/Y'), 0,'L');
$this->SetFont('Arial','B');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,'Cargo Shipping (Ja) Ltd',0,0,'C');
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
$this->Cell( 200, 5,'Cancelled Receipts', 0,1,'C');
$this->SetFont('Arial','');
$this->Cell( 200, 5,$date_range, 0,1,'C');
$this->Ln();
$this->SetFont('Arial','',12);
$this->SetFillColor(220,220,220);
$this->Cell(30,5,' Receipt No.',1,0,'C',true);
$this->Cell(30,5,' Date',1,0,'C',true);
$this->Cell(30,5,' Amount',1,0,'R',true);
$this->Cell(30,5,' Issued By',1,0,'C',true);
$this->Cell(30,5,' Cancelled By',1,0,'C',true);
$this->Cell(30,5,' Cancelled Date',1,1,'C',true);

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
 
 $sql="select r.cancel_date,r.exchange_rate,r.receipt_total,x.user_name as cancel_by,u.user_name,r.payee,r.id,r.receipt_date from ((receipt as r  left join user_profile as u on r.created_by=u.id) left join user_profile as x on r.cancel_by=x.id) where cancelled=1 and r.receipt_date between $request[0] and $request[1] $item_str order by r.id ";
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$s_date= new DateTime($request[1]) ;
$e_date= new DateTime($request[2]) ;
$date_range =$s_date->format('d/m/Y').' to '.$e_date->format('d/m/Y');

$date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$total=0;
$desc='';
$table_index=0;
$g_total=0;
foreach($table_data as $current_rec){

  $desc=$current_rec["description"];
  $table_index++;
  $date = new DateTime($current_rec["receipt_date"]);
  $cancel_date = new DateTime($current_rec["cancel_date"]);
  $pdf->Cell(30,5,sprintf('%08d',$current_rec["id"]),0,0,'C');
  $pdf->Cell(30,5,$date->format('d/m/Y'),0,0,'C');
  $pdf->Cell(30,5,number_format($current_rec["receipt_total"],2),0,0,'R');  
  $pdf->Cell(30,5,$current_rec["user_name"],0,0,'C');
  $pdf->Cell(30,5,$current_rec["cancel_by"],0,0,'C');
  $pdf->Cell(30,5,$cancel_date->format('d/m/Y'),0,1,'C');
  
}
$pdf->Output();

?>
