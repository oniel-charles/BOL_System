<?php
require('fpdf.php');
require_once 'numbersToWords.php';

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
    $this->SetFont('Arial','',10);
    $this->SetFillColor(220,220,220);  
    $this->Cell(17,5,'Order No.',0,0,'C',true);    
    $this->Cell(20,5,'Date',0,0,'C',true);
    $this->Cell(20,5,'Issued By',0,0,'L',true);
    $this->Cell(40,5,'Product',0,0,'L',true);
    $this->Cell(17,5,'Quantity',0,0,'C',true);
    $this->Cell(25,5,'Purchase Price',0,0,'R',true);
    $this->Cell(25,5,'Sale Price ',0,0,'R',true);
    $this->Cell(25,5,'Profit ',0,1,'R',true);

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
$this->SetFont('Arial','B',18);
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
//$this->SetFont('ManuskriptGothisch','',18);
$this->Cell(150,10,$company_rec->company_name,0,0,'C');
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

$this->Cell( 200, 5,$report_filter, 0,1,'C');

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

$mysqli=$this->db->conn;
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


   $order_number=0;
   $user=$claims['full_name'];

   $order_by='';
   $report_filter='Sale Listing';

  $filter_str='';
  if (intval($this->requestUrl[4])!=0){
    $filter_str=' and s.created_by='.$this->requestUrl[4];
  }
  
 $sql="SELECT u.user_name,s.id,s.created_date,pr.description,d.quantity,d.sale_price,(d.quantity * d.sale_price) as sale_price, (p.purchase_price * d.quantity) as purchase_price,((d.sale_price-p.purchase_price)*d.quantity) as profit from (((((sale_order as s left join sale_detail as d on s.id=d.sale_order_id) left join purchase_detail as p on d.purchase_detail_id=p.id) left join purchase_order as po on p.purchase_order_id=po.id) left join product as pr on d.product_id =pr.id)  left join user_profile as u on s.created_by=u.id) where s.cancel_date is null and po.cancel_date is null and s.created_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3].  $filter_str;
 //echo $sql;
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$s_date= new DateTime($this->requestUrl[2]) ;
$e_date= new DateTime($this->requestUrl[3]) ;
$date_range =$s_date->format('d/m/Y').' to '.$e_date->format('d/m/Y');

$run_date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$total=0;
$desc='';
$table_index=0;
$g_total=array();
$method='';
$port='';
$currency='';
$method_total=0;
$profit=0;
foreach($table_data as $current_rec){
  $pdf->SetFont('Arial','',10); 
 

/*
    $this->Cell(17,5,'Order No.',0,0,'C',true);    
    $this->Cell(20,5,'Date',0,0,'C',true);
    $this->Cell(20,5,'Issued By',0,0,'L',true);
    $this->Cell(40,5,'Product',0,0,'L',true);
    $this->Cell(17,5,'Quantity',1,0,'C',true);
    $this->Cell(25,5,'Purchase Price',1,0,'R',true);
    $this->Cell(25,5,'Sale Price ',0,0,'R',true);
    $this->Cell(25,5,'Profit ',0,1,'R',true);
*/
  $method=$current_rec["payment_type"];
  $port=$current_rec["port_code"];
  $currency=$current_rec["currency_code"];
  $table_index++;  
  $pdf->Cell(17,5,sprintf('%08d',$current_rec["id"]),0,0,'C');
  $date = new DateTime($current_rec["created_date"]);
  $pdf->Cell(20,5,$date->format('d/m/Y'),0,0,'C');
  $pdf->Cell(20,5,$current_rec["user_name"],0,0,'L');
  $pdf->Cell(40,5,substr($current_rec["description"],0,21),0,0,'L');
  $pdf->Cell(17,5,$current_rec["quantity"],0,0,'C');  
  
  $pdf->Cell(25,5,number_format($current_rec["purchase_price"],2),0,0,'R');
  $pdf->Cell(25,5,number_format($current_rec["sale_price"],2),0,0,'R');  
  $pdf->Cell(25,5,number_format($current_rec["profit"],2),0,1,'R');  
  $total +=$current_rec["sale_price"];
  $profit +=$current_rec["profit"];
  $method_total +=$current_rec["amount"];
  $port_total +=$current_rec["amount"];
  if (!$g_total[$current_rec["currency_code"]]){$g_total[$current_rec["currency_code"]]=0;}
  $g_total[$current_rec["currency_code"]] +=$current_rec["amount"];
}


     $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(134,5,"TOTAL ",'T',0,'R');
    $pdf->Cell(30,5,number_format($total,2),'T',0,'R');   
    $pdf->Cell(26,5,number_format($profit,2),'T',1,'R');   
  



//  }



$pdf->Output();

?>
