<?php
require('fpdf.php');
require_once 'numbersToWords.php';

class PDF extends FPDF
{

  // Simple table
function ItemGroupHeader()
{
    global $current_rec;
    global $rec;
    $this->SetFont('Arial','UB',12); 
    $this->Cell(200,7,$current_rec["description"],0,1,'C');
    $this->SetFont('Arial','',10);
    $this->SetFillColor(220,220,220);
    $this->Cell(20,5,'Ref No.',0,0,'L',true);
    $this->Cell(40,5,'Shipper',0,0,'L',true);
    $this->Cell(40,5,'Consignee',0,0,'L',true);
    $this->Cell(30,5,'Amount',0,0,'R',true);
    $this->Cell(30,5,'Paid',0,0,'R',true);
    $this->Cell(30,5,'Balance',0,1,'R',true);
}
function ItemGroupFooter()
{
    global $current_rec;
    global $rec;
    $this->SetFont('Arial','UB',12); 
    $this->Cell(50,7,' Total for'.$current_rec["description"],0,0);
    $this->Cell(50,7,' $'.$group_total,0,1);
    $this->SetFont('Arial','',10);    
}

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
  global $currency_header;
  global $rec;

$this->SetFont('Arial','',10);  
$this->Cell( 30, 5,$date->format('d/m/Y'), 0,'L');
$this->SetFont('Arial','B',18);
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
//$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,$company_rec->company_name,0,0,'C');
$this->Cell(1,5,'',0,1,'C');
$this->SetFont('Arial','',10);
$this->Cell( 30, 5,$date->format('h:i a'), 0,'L');
$this->Cell( 150, 5,'', 0);
$this->Cell(1,5,'',0,1);
$this->SetFont('Arial','',10);
$this->Cell( 30, 5,'', 0,'L');	
$this->MultiCell( 150, 5, $company_rec->company_address, 0,'C');
$this->Ln();
$this->Cell(110, 5,'Vessel / Voyage : '.$rec->vessel_name.' / '.$rec->voyage_number, 0,0,'L');
$this->Cell(70, 5,'Sail Date: '.$rec->sail_date->format('d/m/Y'), 0,1);
$this->Cell(110, 5,'Manifest# : '.$rec->manifest_number, 0,0,'L');
$this->Cell(70, 5,'Container : '.$rec->container_number, 0,1);


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

  global $company_rec;
  global $bill_rec;
  global $date;
  global $receipt_number;
  global $date_range;
  global $table_data;
  global $table_index;
  global $group_total;
  global $currency_header;
  global $current_rec;
  global $rec;

$mysqli=$this->db->conn;
date_default_timezone_set('America/Jamaica');
 error_reporting(E_ERROR | E_PARSE);
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$post_data = json_decode(file_get_contents('php://input'),true);

//var_dump($post_data);

//exit(' oniel exit');
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
$sql="SELECT b.*,v.vessel_name,c.container_number FROM ((booking as b left join vessel as v on b.vessel_id=v.id)  left join booking_container as c on b.id=c.booking_id ) where b.id=".$this->requestUrl[2];
$result = mysqli_query($mysqli,$sql);
$rec= mysqli_fetch_object($result);	
$rec->sail_date= new DateTime($rec->sail_date);

 $sql="SELECT b.bill_of_lading_number,b.shipper_fname,b.shipper_sname,b.consignee_fname,b.consignee_sname,o.amount,o.amount_paid,c.description from ((bill_of_lading as b left join bill_of_lading_other_charge as o on b.id=o.billoflading_id) left join charge_item as c on o.charge_item_id=c.id) where b.booking_id=".$this->requestUrl[2]." and b.parent_bol=0 and o.amount-amount_paid>0 ";
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}
$currency_header=' ';


$date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$total=0;
$desc='';
$table_index=0;
$g_total=0;
foreach($table_data as $current_rec){
  if ($current_rec["description"]!=$desc && $table_index>0){
    if ($total>0){
      $pdf->SetFont('Arial','B',10); 
      $pdf->Cell(170,5,'TOTAL','T',0,'R');
      $pdf->Cell(30,5,number_format($total,2),'T',1,'R');   
      $pdf->SetFont('Arial','',10); 
      $pdf->Ln();
      $total=0;
    }

    $pdf->ItemGroupHeader();
  }
  /*
      $this->Cell(25,5,'Reference No.',0,0,'L',true);
    $this->Cell(35,5,'Shipper',0,0,'C',true);
    $this->Cell(35,5,'Consignee',0,0,'L',true);
    $this->Cell(40,5,'Amount',0,0,'L',true);
    $this->Cell(40,5,'Paid',0,0,'L',true);
    $this->Cell(40,5,'Balance',0,0,'L',true);
  */
  $desc=$current_rec["description"];
  $table_index++;
  $pdf->Cell(20,5,$current_rec["bill_of_lading_number"],0,0,'L');
  $pdf->Cell(40,5,$current_rec["shipper_fname"].' '.$current_rec["shipper_sname"],0,0,'L');
  $pdf->Cell(40,5,$current_rec["consignee_fname"].' '.$current_rec["consignee_sname"],0,0,'L');
  $pdf->Cell(30,5,$current_rec["amount"],0,0,'R');
  $pdf->Cell(30,5,$current_rec["amount_paid"],0,0,'R');
  $pdf->Cell(30,5,number_format($current_rec["amount"]-$current_rec["amount_paid"],2),0,1,'R');
  
  $total +=number_format($current_rec["amount"]-$current_rec["amount_paid"],2);
  $g_total +=number_format($current_rec["amount"]-$current_rec["amount_paid"],2);
  
}
$pdf->SetFont('Arial','B',10); 
$pdf->Cell(160,5,'TOTAL','T',0,'R');
$pdf->Cell(30,5,number_format($total,2),'T',1,'R');   
$pdf->SetFont('Arial','',10); 

$pdf->Output();

?>
