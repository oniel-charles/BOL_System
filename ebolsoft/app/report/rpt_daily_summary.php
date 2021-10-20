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
    $this->Cell(20,5,'Port',0,0,'L',true);
    $this->Cell(25,5,'',0,0,'L',true);    
    $this->Cell(20,5,'$',0,0,'C',true);
    $this->Cell(20,5,'Cash',0,0,'R',true);
    $this->Cell(20,5,'Debit Card',0,0,'R',true);
    $this->Cell(20,5,'Credit Card',0,0,'R',true);
    $this->Cell(20,5,'Cheque',0,0,'R',true);
    $this->Cell(20,5,'D. Deposit',0,0,'R',true);
    $this->Cell(20,5,'Total',0,1,'R',true);
    //$this->Cell(20,5,'Amount',0,1,'R',true);

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

  $this->Cell( 200, 5,'SUMMARY LISTING', 0,1,'C');
 
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

 //Transport
 $sql="SELECT data_value from system_values where code='TRNSPRTID'";
 $result = mysqli_query($mysqli,$sql);
 $transport= mysqli_fetch_object($result);	

 
 //Pre-Clearance ID
 $sql="SELECT data_value from system_values where code='PRECLRID'";
 $result = mysqli_query($mysqli,$sql);
 $preclear= mysqli_fetch_object($result);	

   $order_number=0;
   $user=$claims['full_name'];

   $order_by='';
   switch ($this->requestUrl[4]) {
    case 'byuser':    
    $order_by=" order by u.user_name,c.currency_code,p.payment_type,r.id  ";
    break;
    case 'byday':    
    $order_by=" order by r.receipt_date,c.currency_code,p.payment_type,r.id  ";
    break;
  }   
  $filter_str='';
  if (intval($this->requestUrl[5])!=0 && $this->requestUrl[4]=='byuser'){
    $filter_str=' and r.created_by='.$this->requestUrl[5];
  }
  if($post_data["report_type"]=='F'){
    $filter_str=$filter_str." and d.charge_item_id is not null ";
    $report_filter='(Freight)';
  }
  if($post_data["report_type"]=='N'){
    $filter_str=$filter_str." and d.charge_item_id is  null ";
    $report_filter='(Excluding Freight)';
  }
  $sql="select po.port_code,sum(d.loc_amt) as amt,c.currency_code,'Cash' as payment_type,'Transportation' as user_name from ((((((receipt as r left join receipt_payment as p on r.id=p.receipt_id ) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join currency as c on r.currency_id=c.id) left join (select amount as loc_amt,receipt_id,charge_item_id from receipt_detail where charge_item_id=$transport->data_value) as d on r.id=d.receipt_id) where (r.cancelled is null or r.cancelled=0) and d.receipt_id is not null and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]." group by po.port_code,c.currency_code ";
  $transport_data = mysqli_query($mysqli,$sql);	
  $sql="select po.port_code,sum(d.loc_amt) as amt,c.currency_code,'Cash' as payment_type,'PreClear Income' as user_name from ((((((receipt as r left join receipt_payment as p on r.id=p.receipt_id ) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join currency as c on r.currency_id=c.id) left join (select amount as loc_amt,receipt_id,charge_item_id from receipt_detail where charge_item_id=$preclear->data_value) as d on r.id=d.receipt_id) where (r.cancelled is null or r.cancelled=0) and d.receipt_id is not null and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]." group by po.port_code,c.currency_code ";
  $preclear_income_data = mysqli_query($mysqli,$sql);	
  $sql="select 'Cash' as payment_type,'Preclearance' as user_name,po.port_code,c.currency_code,sum(p.local_total) as amt from (((((preclearance as p left join receipt as r on p.billoflading_id=r.billoflading_id ) left join currency as c on r.currency_id=c.id) left join bill_of_lading as b on p.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) where (r.cancelled is null or r.cancelled=0) and r.receipt_date is not null and (p.cancelled is null or p.cancelled=0)  and p.preclearance_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]." group by po.port_code,c.currency_code order by po.port_code ";
  $preclearance_data = mysqli_query($mysqli,$sql);	
  $sql="select po.port_code,c.currency_code,'Cash' as payment_type,sum(d.amount) as amt ,'Total' as user_name from ((((((((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join preclearance_detail as p on d.bol_id=p.bol_id and d.charge_item_id=p.charge_item_id) left join preclearance as pre on p.preclearance_id=pre.id) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join user_profile as u on r.created_by=u.id) left join currency as c on r.currency_id=c.id) where (r.cancelled is null or r.cancelled=0) and (pre.id or pre.cancelled=1 is null ) and d.charge_item_id <>$freight->data_value and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]." group by po.port_code,c.currency_code";
  $total_data = mysqli_query($mysqli,$sql);	

 //$sql="select r.exchange_rate,d.currency_amount,u.user_name,r.payee,r.id,r.receipt_date,d.amount,c.description from (((receipt as r left join receipt_detail as d on r.id=d.receipt_id ) left join charge_item as c on d.charge_item_id=c.id) left join user_profile as u on r.created_by=u.id) where  (r.cancelled is null or r.cancelled=0) and r.receipt_date between $this->requestUrl[2] and $request[1] $filter_str $order_by  ";
 $sql="select po.port_code,c.currency_code,p.payment_type,sum(p.amount) as amt ,u.user_name from 
 (((((((receipt as r left join receipt_payment as p on r.id=p.receipt_id ) left join bill_of_lading as b on r.billoflading_id=b.id) left join voyage as v on b.voyage_id=v.id) left join port as po on b.port_of_origin=po.id) left join user_profile as u on r.created_by=u.id) left join currency as c on r.currency_id=c.id) left join (select amount as freight_amount,receipt_id,charge_item_id from receipt_detail where charge_item_id=$freight->data_value) as d on r.id=d.receipt_id)  where (r.cancelled is null or r.cancelled=0) and d.charge_item_id is null and r.receipt_date between ".$this->requestUrl[2]." and ".$this->requestUrl[3]." group by po.port_code,c.currency_code,p.payment_type,u.user_name order by po.port_code,u.user_name";
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
$g_total=0;
$method='';
$cur='';
$break_total=0;
$cur_total;
//$receipt_array["fc_us"]["details"][] = array("discount"=>$discount_amount,"bill_amount"=>$rec->amount,"rec_date"=>$rec->receipt_date,"rec_id"=>$rec->id,"paid"=>$paid,"roe"=>$us_rate->exchange_rate,"currency_amount"=>$currency_amount,"receipt_number"=>0,"date"=>0,"item"=>$rec->charge_item_id,"bill_currency"=>$rec->bill_currency,"item_currency"=>$rec->item_currency, "gct"=> $rec->attract_gct,"description"=> $rec->description, "amount" => $us_amount); 
 //{value:"Cash",text:"Cash"},{value:"Debit Card",text:"Debit Card"},{value:"Credit Card",text:"Credit Card"},{value:"Cheque",text:"Cheque"},{value:"Direct Deposit",text:"Direct Deposit"}];
$summary_array=array();
$summary_breakdown=array();
foreach($table_data as $current_rec){
  if (!$summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]){
    $summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]=array("Cash"=>0,"Debit Card"=>0,"Credit Card"=>0,"Cheque"=>0,"Direct Deposit"=>0);
  }
  $summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]][$current_rec["payment_type"]]=$current_rec["amt"];
}
//var_dump($summary_array);
//exit();

foreach($total_data  as $current_rec){
  if (!$summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]){
    $summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]=array("Cash"=>0,"Debit Card"=>0,"Credit Card"=>0,"Cheque"=>0,"Direct Deposit"=>0);
  }
  $summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]][$current_rec["payment_type"]]=$current_rec["amt"];
}

foreach($preclearance_data as $current_rec){
  if (!$summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]){
    $summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]=array("Cash"=>0,"Debit Card"=>0,"Credit Card"=>0,"Cheque"=>0,"Direct Deposit"=>0);
  }
  $summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]][$current_rec["payment_type"]]=$current_rec["amt"];
}
foreach($transport_data as $current_rec){
  if (!$summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]){
    $summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]=array("Cash"=>0,"Debit Card"=>0,"Credit Card"=>0,"Cheque"=>0,"Direct Deposit"=>0);
  }
  $summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]][$current_rec["payment_type"]]=$current_rec["amt"];
}
foreach($preclear_income_data as $current_rec){
  if (!$summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]){
    $summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]=array("Cash"=>0,"Debit Card"=>0,"Credit Card"=>0,"Cheque"=>0,"Direct Deposit"=>0);
  }
  $summary_breakdown[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]][$current_rec["payment_type"]]=$current_rec["amt"];
}


//var_dump($summary_array);
  $pdf->SetFont('Arial','',10); 
  $field=new DateTime($current_rec["receipt_date"]);
  $field=$field->format('d/m/Y');
  
foreach($summary_array as $x => $x_value){ 
  foreach($x_value as $y => $y_value ){
    foreach($y_value as $z => $z_value ){
      //if (!$summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]
      $total=$summary_array[$x][$y][$z]["Cash"]+$summary_array[$x][$y][$z]["Debit Card"]+$summary_array[$x][$y][$z]["Credit Card"]+$summary_array[$x][$y][$z]["Cheque"]+$summary_array[$x][$y][$z]["Direct Deposit"];
  $pdf->SetFont('Arial','',10);    
   if($y=='Total'){$pdf->SetFont('Arial','B',10);
    $summary_array[$x][$y][$z]["Cash"]=''; $summary_array[$x][$y][$z]["Debit Card"]=''; $summary_array[$x][$y][$z]["Credit Card"]='';$summary_array[$x][$y][$z]["Cheque"]='';$summary_array[$x][$y][$z]["Direct Deposit"]='';
   }   
   $pdf->Cell(25,5,$z,0,0,'L');
  $pdf->Cell(20,5,$x,0,0,'C');
  $pdf->Cell(20,5,$y,0,0,'C');
  $pdf->Cell(20,5,$summary_array[$x][$y][$z]["Cash"],0,0,'R');
  $pdf->Cell(20,5,$summary_array[$x][$y][$z]["Debit Card"],0,0,'R');
  $pdf->Cell(20,5,$summary_array[$x][$y][$z]["Credit Card"],0,0,'R');
  $pdf->Cell(20,5,$summary_array[$x][$y][$z]["Cheque"],0,0,'R');
  $pdf->Cell(20,5,$summary_array[$x][$y][$z]["Direct Deposit"],0,0,'R');
  $pdf->Cell(20,5,number_format($total,2),0,1,'R');
  if($y=='Total'){$pdf->Ln();}
    }
  }
} 
$pdf->SetFont('Arial','BU',10);
$pdf->Cell(185,5,'TRANSPORTATION AND PRE-CLEARANCE',0,1,'C');
foreach($summary_breakdown as $x => $x_value){ 
  foreach($x_value as $y => $y_value ){
    foreach($y_value as $z => $z_value ){
      //if (!$summary_array[$current_rec["port_code"]][$current_rec["user_name"]][$current_rec["currency_code"]]
      $total=$summary_breakdown[$x][$y][$z]["Cash"]+$summary_breakdown[$x][$y][$z]["Debit Card"]+$summary_breakdown[$x][$y][$z]["Credit Card"]+$summary_breakdown[$x][$y][$z]["Cheque"]+$summary_breakdown[$x][$y][$z]["Direct Deposit"];
  $pdf->SetFont('Arial','',10);    
   if($y=='Total'){$pdf->SetFont('Arial','B',10);
    $summary_breakdown[$x][$y][$z]["Cash"]=''; $summary_breakdown[$x][$y][$z]["Debit Card"]=''; $summary_breakdown[$x][$y][$z]["Credit Card"]='';$summary_breakdown[$x][$y][$z]["Cheque"]='';$summary_breakdown[$x][$y][$z]["Direct Deposit"]='';
   }   
   $pdf->Cell(25,5,$z,0,0,'L');
  $pdf->Cell(20,5,$x,0,0,'C');
  $pdf->Cell(20,5,$y,0,0,'C');
  $pdf->Cell(20,5,$summary_breakdown[$x][$y][$z]["Cash"],0,0,'R');
  $pdf->Cell(20,5,$summary_breakdown[$x][$y][$z]["Debit Card"],0,0,'R');
  $pdf->Cell(20,5,$summary_breakdown[$x][$y][$z]["Credit Card"],0,0,'R');
  $pdf->Cell(20,5,$summary_breakdown[$x][$y][$z]["Cheque"],0,0,'R');
  $pdf->Cell(20,5,$summary_breakdown[$x][$y][$z]["Direct Deposit"],0,0,'R');
  $pdf->Cell(20,5,number_format($total,2),0,1,'R');
  if($y=='Total'){$pdf->Ln();}
    }
  }
} 
  $pdf->SetFont('Arial','',10); 



$pdf->Output();

?>
