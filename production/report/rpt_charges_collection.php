<?php
require('fpdf.php');
require_once '../user_access.php';
require_once '../numbersToWords.php';

class PDF extends FPDF
{

  // Simple table
function ItemGroupHeader()
{
    global $current_rec;
    $this->SetFont('Arial','UB',12); 
    $this->Cell(200,7,$current_rec["description"],0,1);
    $this->SetFont('Arial','',10);
    $this->SetFillColor(220,220,220);
    $this->Cell(19,5,'Receipt No.',0,0,'L',true);
    $this->Cell(20,5,'Date',0,0,'C',true);
    $this->Cell(11,5,'Port',0,0,'L',true);
    $this->Cell(50,5,'Customer',0,0,'L',true);
    $this->Cell(30,5,'Issued By',0,0,'L',true);
    //$this->Cell(30,5,'US Amount',0,0,'R',true);
    $this->Cell(70,5,'Amount',0,1,'R',true);

}
function ItemGroupFooter()
{
    global $current_rec;
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

$this->SetFont('Arial','');  
$this->Cell( 30, 5,$date->format('d/m/Y'), 0,'L');
$this->SetFont('Arial','B');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,'Cargo Shipping (Ja) Ltd',0,0,'C');
$this->Cell(1,5,'',0,1,'C');
$this->SetFont('Arial','',10);
$this->Cell( 30, 5,$date->format('h:i a'), 0,'L');
$this->Cell( 150, 5,'', 0);
$this->Cell(1,5,'',0,1);
$this->SetFont('Arial','',10);
$this->Cell( 30, 5,'', 0,'L');	
$this->MultiCell( 150, 5, $company_rec->company_address, 0,'C');
$this->Ln();

$this->SetFont('Arial','UB');
$this->Cell( 200, 5,'Detail Charge Collection '.$currency_header, 0,1,'C');
$this->SetFont('Arial','');
$this->Cell( 200, 5,$date_range, 0,1,'C');
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
  $item_str='';
  if (intval( $request[2])!=0){
       $item_str=' and d.charge_item_id='.intval( $request[2]);
  }
  $item_str=' and d.charge_item_id in ('.implode("," ,$post_data["charge_ids"]).') and r.currency_id ='.$post_data["currency_id"];
 
 $sql="select p.port_code,r.exchange_rate,d.currency_amount,u.user_name,r.payee,r.id,r.receipt_date,d.amount,c.currency_id,c.description from (((((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) left join bill_of_lading as b on r.billoflading_id=b.id) left join port as p on b.port_of_loading=p.id) where  (r.cancelled is null or r.cancelled=0) and b.port_of_loading in (".implode("," ,$post_data["port_ids"]).") and r.receipt_date between $request[0] and $request[1] $item_str order by c.description,r.receipt_date ";
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}
$currency_header=' - JMD';
if ($post_data["currency_id"]==2){
  $currency_header=' - USD';
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
  $us_rate=''; $us_amount='';
  if ($current_rec["currency_id"]==2){
    $us_rate=' @ R.O.E '.$current_rec["exchange_rate"];
    $us_amount=$current_rec["currency_amount"];
  }
  $desc=$current_rec["description"];
  $table_index++;
  $pdf->Cell(19,5,sprintf('%08d',$current_rec["id"]),0,0,'L');
  $date = new DateTime($current_rec["receipt_date"]);
  $pdf->Cell(20,5,$date->format('d/m/Y'),0,0,'L');
  $pdf->Cell(11,5,substr($current_rec["port_code"],0,4),0,0,'L');
$y=$pdf->GetY();	
$x=$pdf->GetX()+50;	
$pdf->MultiCell( 50, 5, $current_rec["payee"], 0,'L');
$next_line=$pdf->GetY();
$pdf->SetY($y);
$pdf->SetX($x);

 // $pdf->Cell(50,5,$current_rec["payee"],0,0,'L');
  $pdf->Cell(30,5,substr($current_rec["user_name"],0,17),0,0,'L');
  $pdf->SetFont('Arial','',10); 
 /* $pdf->Cell(15,5,$us_amount,0,0,'R');
  $pdf->SetFont('Arial','',8); 
  $pdf->Cell(15,5,$us_rate,0,0,'L');
  $pdf->SetFont('Arial','',10);
  */ 
  $pdf->Cell(70,5,number_format($current_rec["amount"],2),0,1,'R');  
  $total +=$current_rec["amount"];
  $g_total +=$current_rec["amount"];
  $pdf->SetY($next_line);
}
$pdf->SetFont('Arial','B',10); 
$pdf->Cell(160,5,'TOTAL','T',0,'R');
$pdf->Cell(40,5,number_format($total,2),'T',1,'R');   
$pdf->SetFont('Arial','',10); 

$pdf->Output();

?>
