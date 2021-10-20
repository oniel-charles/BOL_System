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
  global $date_range;

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
$this->Cell( 200, 5,'Daily Orders', 0,1,'C');
$this->SetFont('Arial','');
$this->Cell( 200, 5,$date_range, 0,1,'C');
$this->Ln();

$this->SetFont('Arial','',12);
$this->SetFillColor(220,220,220);
$this->Cell(25,5,' Order No',1,0,'C',true);
$this->Cell(50,5,' Consignee',1,0,'C',true);
$this->Cell(50,5,' BL# [ Ref No ]',1,0,'C',true);
$this->Cell(50,5,' Voyage',1,0,'C',true);
$this->Cell(25,5,' Order Date',1,1,'C',true);

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
 
 $sql="SELECT o.id,o.order_date,v.arrival_date,v.voyage_number,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name FROM ((((shipment_order as o left join `bill_of_lading` as b on o.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) where o.order_date between $request[0] and $request[1] $item_str order by o.id ";
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
foreach($table_data as $current_rec){

  $desc=$current_rec["description"];
  $table_index++;
  $date = new DateTime($current_rec["order_date"]);
  $arrival_date = new DateTime($current_rec["arrival_date"]);
  $pdf->Cell(25,5,sprintf('%08d',$current_rec["id"]),0,0,'C');
  $pdf->Cell(50,5,$current_rec["consignee_name"],0,0,'L');  
  $pdf->Cell(50,5,$current_rec["bill_of_lading_number"].' [ '.$current_rec["refnum"].' ]',0,0,'L');
  $pdf->Cell(50,5,$current_rec["voyage_number"].' - '.$arrival_date->format('d/m/Y'),0,0,'C');
  $pdf->Cell(25,5,$date->format('d/m/Y'),0,1,'C');
  

}
$pdf->Output();

?>
