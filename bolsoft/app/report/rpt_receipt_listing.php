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
    $this->Cell(15,5,'Port',0,0,'L',true);    
    $this->Cell(15,5,'Man#',0,0,'L',true);    
    $this->Cell(17,5,'Receipt No.',0,0,'C',true);    
    $this->Cell(20,5,'Date',0,0,'C',true);
    $this->Cell(20,5,'Type',0,0,'C',true);
    $this->Cell(50,5,'Customer',0,0,'L',true);
    $this->Cell(2,5,' ',0,0,'L',true);
    $this->Cell(20,5,'Issued By',0,0,'L',true);
    $this->Cell(5,5,'',0,0,'L',true);
    $this->Cell(25,5,'Amount',0,1,'R',true);

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

 $sql="SELECT data_value from system_values where code='freight_id'";
 $result = mysqli_query($mysqli,$sql);
 $freight= mysqli_fetch_object($result);	

   $order_number=0;
   $user=$claims['full_name'];

   $order_by='';
   $report_filter='Receipt Listing';
   switch ($this->requestUrl[4]) {
    case 'byuser':    
    $order_by=" order by po.port_code,u.user_name,c.currency_code,p.payment_type,r.id  ";
    break;
    case 'byday':    
    $order_by=" order by po.port_code,r.receipt_date,c.currency_code,p.payment_type,r.id  ";
    break;
  }   
  $filter_str='';
  if (intval($this->requestUrl[5])!=0 && $this->requestUrl[4]=='byuser'){
    $filter_str=' and r.created_by='.$this->requestUrl[5];
  }
  if($post_data["report_type"]=='F'){
    $filter_str=$filter_str." and d.charge_item_id =".$freight->data_value;
    $report_filter='(Freight)';
  }
  if($post_data["report_type"]=='N'){
    $filter_str=$filter_str." and d.charge_item_id  is null";//.$freight->data_value;
    $report_filter='(Excluding Freight)';
  }
 
 //$sql="select v.manifest_number,p.port_code,r.payment_type,avg(r.receipt_total) as amt ,r.exchange_rate,sum(d.currency_amount) as currency_amt,u.user_name,r.payee,r.id,r.receipt_date,sum(d.amount) as dtl_sum from ((((((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as p on b.port_of_origin=p.id) where (r.cancelled is null or r.cancelled=0) and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]."  $filter_str group by v.manifest_number,p.port_code,r.payment_type,r.currency_id,u.user_name,r.payee,r.id,r.receipt_date  $order_by";   
 $sql="select v.manifest_number,po.port_code,d.freight_amount,d.charge_item_id,c.currency_code,p.payment_type,p.amount ,u.user_name,r.payee,r.id,r.receipt_date from 
 (((((((receipt as r left join receipt_payment as p on r.id=p.receipt_id ) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join user_profile as u on r.created_by=u.id) left join currency as c on r.currency_id=c.id) left join (select amount as freight_amount,receipt_id,charge_item_id from receipt_detail where charge_item_id=$freight->data_value) as d on r.id=d.receipt_id)  where (r.cancelled is null or r.cancelled=0) and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]."  $filter_str   $order_by";   

 if($post_data["report_type"]=='P'){
  $report_filter='Preclearance Only';
  $sql="select v.manifest_number,po.port_code,c.currency_code,'' as payment_type,sum(d.amount) as amount ,u.user_name,r.payee,r.id,r.receipt_date from 
  ((((((((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join preclearance_detail as p on d.bol_id=p.bol_id and d.charge_item_id=p.charge_item_id) left join preclearance as pre on p.preclearance_id=pre.id) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join user_profile as u on r.created_by=u.id) left join currency as c on r.currency_id=c.id)   where (r.cancelled is null or r.cancelled=0)  and (pre.id is not null and pre.cancelled is null or pre.cancelled=0) and d.charge_item_id <> $freight->data_value  and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]."  $filter_str group by v.manifest_number,po.port_code,c.currency_code,u.user_name,r.payee,r.id,r.receipt_date order by c.currency_code,po.port_code,u.user_name,r.id ";
 }
 if($post_data["report_type"]=='PF'){
  $report_filter='Exclude Preclearance ';
  $sql="select v.manifest_number,po.port_code,c.currency_code,'' as payment_type,sum(d.amount) as amount ,u.user_name,r.payee,r.id,r.receipt_date from 
  ((((((((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join preclearance_detail as p on d.bol_id=p.bol_id and d.charge_item_id=p.charge_item_id) left join preclearance as pre on p.preclearance_id=pre.id) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join user_profile as u on r.created_by=u.id) left join currency as c on r.currency_id=c.id)   where (r.cancelled is null or r.cancelled=0)  and (pre.id is null  or pre.cancelled=0)  and d.charge_item_id <> $freight->data_value  and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]."  $filter_str group by v.manifest_number,po.port_code,c.currency_code,u.user_name,r.payee,r.id,r.receipt_date order by c.currency_code,po.port_code,u.user_name,r.id ";
 }
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
foreach($table_data as $current_rec){
  $pdf->SetFont('Arial','',10); 
 
  if ($current_rec["currency_code"]!=$currency || $current_rec["user_name"]!=$desc || $current_rec["port_code"]!=$port || $current_rec["payment_type"]!=$method && $table_index>0){
    if ($method_total>0){
      $pdf->SetFont('Arial','B',10);
      $pdf->Cell(162,5,"TOTAL $currency ".$method.' for '.$desc,'T',0,'R');
      $pdf->Cell(30,5,number_format($method_total,2),'T',1,'R');   
      $pdf->SetFont('Arial','',10); 
      $pdf->Ln();
      $method_total=0;
    }
    $method=$current_rec["payment_type"];
  }
  if ($current_rec["currency_code"]!=$currency || $current_rec["port_code"]!=$port || ($this->requestUrl[4]=='byuser' && $current_rec["user_name"]!=$desc && $table_index>0)){
    if ($total>0){
      $pdf->SetFont('Arial','B',10); 
      $pdf->Cell(162,5,"TOTAL $currency for ".$desc,'T',0,'R');
      $pdf->Cell(30,5,number_format($total,2),'T',1,'R');   
      $pdf->SetFont('Arial','',10); 
      $pdf->Ln();
      $total=0;
    }
  }
  if ($current_rec["currency_code"]!=$currency || $current_rec["port_code"]!=$port || ($this->requestUrl[4]=='bydate' && $current_rec["receipt_date"]!=$desc && $table_index>0)){
    if ($total>0){
      $pdf->SetFont('Arial','B',10); 
      $date = new DateTime($desc);
      $pdf->Cell(162,5,"TOTAL $currency for ".$date->format('d/m/Y'),'T',0,'R');
      $pdf->Cell(30,5,number_format($total,2),'T',1,'R');   
      $pdf->SetFont('Arial','',10); 
      $pdf->Ln();
      $total=0;
    }
  }

  if ($current_rec["currency_code"]!=$currency || $current_rec["port_code"]!=$port && $table_index>0){
    if ($port_total>0){
      $pdf->SetFont('Arial','B',10);
      $pdf->Cell(162,5,"TOTAL $currency for ".$port,'T',0,'R');
      $pdf->Cell(30,5,number_format($port_total,2),'T',1,'R');   
      $pdf->SetFont('Arial','',10); 
      $pdf->Ln();
      $port_total=0;
    }
    $port=$current_rec["port_code"];
  }

  if ($this->requestUrl[4]=='byuser'){$desc=$current_rec["user_name"];}
  if ($this->requestUrl[4]=='bydate'){$desc=$current_rec["receipt_date"];}
  $method=$current_rec["payment_type"];
  $port=$current_rec["port_code"];
  $currency=$current_rec["currency_code"];
  $table_index++;  
  $pdf->Cell(15,5,$current_rec["port_code"],0,0,'L');
  $pdf->Cell(15,5,$current_rec["manifest_number"],0,0,'L');
  $pdf->Cell(17,5,sprintf('%08d',$current_rec["id"]),0,0,'C');
  $date = new DateTime($current_rec["receipt_date"]);
  $pdf->Cell(20,5,$date->format('d/m/Y'),0,0,'C');
  $pdf->Cell(20,5,$current_rec["payment_type"],0,0,'C');
  $pdf->Cell(50,5,substr($current_rec["payee"],0,21),0,0,'L');
  $pdf->Cell(2,5,'',0,0,'L');
  $pdf->Cell(20,5,$current_rec["user_name"],0,0,'L');
  $pdf->SetFont('Arial','',10); 
  $pdf->Cell(8,5,$current_rec["currency_code"],0,0,'L');
  $pdf->Cell(25,5,number_format($current_rec["amount"],2),0,1,'R');  
  $total +=$current_rec["amount"];
  $method_total +=$current_rec["amount"];
  $port_total +=$current_rec["amount"];
  if (!$g_total[$current_rec["currency_code"]]){$g_total[$current_rec["currency_code"]]=0;}
  $g_total[$current_rec["currency_code"]] +=$current_rec["amount"];
}


 // if ($total>0){
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(162,5,"TOTAL $currency ".$method.' for '.$desc,'T',0,'R');
    $pdf->Cell(30,5,number_format($method_total,2),'T',1,'R');   
  

    if ($this->requestUrl[4]=='byuser'){ 
      $pdf->Cell(162,5,"TOTAL $currency for Cashier ".$desc,'T',0,'R');
    }
    if ($this->requestUrl[4]=='bydate'){ 
      $date = new DateTime($desc);
      $pdf->Cell(162,5,"TOTAL $currency for ".$date->format('d/m/Y'),'T',0,'R');
    }
    $pdf->Cell(30,5,number_format($total,2),'T',1,'R');   
    $pdf->SetFont('Arial','',10); 
    $pdf->Ln();

    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(162,5,"TOTAL $currency for ".$port,'T',0,'R');
    $pdf->Cell(30,5,number_format($port_total,2),'T',1,'R'); 
//  }

$pdf->Ln();
foreach($g_total as $x => $x_value){ 
  $pdf->SetFont('Arial','B',10); 
  $pdf->Cell(162,5,"GRAND TOTAL $x",'T',0,'R');
  $pdf->Cell(30,5,number_format($x_value,2),'T',1,'R');   
  $pdf->SetFont('Arial','',10); 
}

$pdf->Output();

?>
