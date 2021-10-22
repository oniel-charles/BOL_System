<?php
require('fpdf.php');
require_once 'numbersToWords.php';

class PDF extends FPDF
{
function Header()
{
  global $company_rec;
  global $bill_rec;
  global $date;
  global $po_number;
  global $roe_str;
  global $currency_code;
  global $reprint;
  global $details;
  global $sale_rec;

  $this->SetFont('Arial','B');
$this->Cell( 32, 5,'Date ', 0,'L');
$this->Cell( 20, 5,'', 0,'L');
$this->AddFont('ManuskriptGothisch','','ManuskriptGothischUNZ1A.php');
//$this->SetFont('ManuskriptGothisch','',26);
$this->SetFont('Arial','B',12);
$this->Image('../img/company_icon.jpg',34,8,-250);
$this->SetX($this->GetX()-16);
$this->Cell(118,5,$company_rec->company_name,0,1);
$x=$this->GetX()+30;
$y=$this->GetY();	
$this->SetFont('Arial','',12);
$this->Cell( 30, 5,$date->format('M d, Y'), 0,1);


$this->SetFont('Arial','',12);
$this->Cell( 30, 5,$date->format('h:i a'), 0,'L');
$this->SetY($y);
$this->SetX(35);
$this->MultiCell( 118, 5, $company_rec->company_address, 0,'C');

//$this->SetY($y);
//$this->SetX(100);
$this->Ln();

$this->SetFont('Arial','B',15);
$this->Cell( 90, 5,'Sale Order ', 0,0,'R');
$this->SetFont('Arial','B',12);
$this->Cell(50,5,sprintf('%08d',$sale_rec->id),0,1,'R');
$this->SetTextColor(255,0,0);
$this->Cell( 140, 5,$reprint, 0,1,'R');
$this->SetTextColor(0,0,0);
$this->Ln();
$this->SetFont('Arial','B',12);
$this->Cell( 25, 5,'Customer: ', 0,0);
$this->SetFont('Arial','',12);
$this->Cell( 80, 5,$sale_rec->customer_name, 0,0);
$this->Ln();


$this->SetFont('Arial','',12);



$this->SetX(130);

if($this->GetY()<$next_y){
  $this->SetY($next_y);
}

//$this->SetX($x);



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
  global $po_number;
  global $roe_str;
  global $currency_code;
  global $reprint;
  global $details;
  global $sale_rec;

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

$mysqli=$this->db->conn;
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
    
    
  $service_type ="charges";
  if ( $this->requestUrl[3]=='reprint') {
    $rec_id=$this->requestUrl[4];
    $service_type ="reprint";
  }
  
  $roe_str='';
  $currency_code='JMD';
  $payment_type='';
  $payment_type_no='';

  $bl_id=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
  $filter=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[4]);
  $print_separate=$post_data["separate"];



  
  //Get detail
  $detsql="SELECT measure,measure_unit,b.Description_of_goods,package.description, `number_of_items`,`weight`,`weight_unit` FROM `bill_of_lading_detail` as b  left join package on b.package_type_id=package.id WHERE b.billoflading_id=".$bill_rec->id;
  $details = mysqli_query($mysqli,$detsql);

  $sql="SELECT data_value from system_values where code='printorder'";
  $result = mysqli_query($mysqli,$sql);
  $system_code= mysqli_fetch_object($result);	

//$pdf = new PDF('L','mm',array(177.8,215.9));
$pdf = new PDF('P','mm',array(177.8,177.8));
//$pdf = new PDF();
//$pdf->AddPage('P',array(215.9,177.8),0);

if ( $this->requestUrl[2]=='create'){
  $reprint="";
  mysqli_autocommit($mysqli,FALSE);

  //Add new client
  $customer_id=$this->post_data["sale_order"]["customer_id"];
  if ($this->post_data["sale_order"]["customer_id"]==0 && $this->post_data["sale_order"]["phone_number"] !=""){
      $sql = " INSERT INTO `client` ( `client_fname`,`client_sname`, `client_address`, `phone_number`)  ";
      $sql =$sql. " VALUES ('".$this->post_data["sale_order"]["customer_name"]."',' ','".$this->post_data["sale_order"]["customer_address"]."','".$this->post_data["sale_order"]["phone_number"]."')";
            
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        if (!$result) { handleRollBack($mysqli);}
        die($sql.'<br>'.mysqli_error($mysqli));
      } 
      $customer_id = mysqli_insert_id($mysqli);
  }
  $sql = " INSERT INTO `sale_order` ( `customer_id`, `customer_name`, `amount`, `created_by`,  `created_date`, `created_time`)  ";
  $sql =$sql. " VALUES (".$customer_id.",'".$this->post_data["sale_order"]["customer_name"]."',".$this->post_data["sale_order"]["amount"].",".$this->claims['id'].",".date('Ymd').",".date('his').")"; //TBC created by      
       
  $result = mysqli_query($mysqli,$sql);
  if (!$result) {
    http_response_code(404);
    if (!$result) { handleRollBack($mysqli);}
    die($sql.'<br>'.mysqli_error($mysqli));
  }
  $sale_id=mysqli_insert_id($mysqli);
  $detail_sql = " INSERT INTO `sale_detail` (`sale_order_id`, `product_id`, `quantity`, `sale_price`, `purchase_detail_id`) VALUES";

  
  for ($i = 0; $i < count($this->post_data["detail"]); $i++) {
      $quantity=$this->post_data["detail"][$i]["quantity"];
      $sql="SELECT d.id,d.balance_quantity, d.purchase_price FROM `purchase_detail` as d left join purchase_order p on d.purchase_order_id=p.id WHERE d.product_id=".$this->post_data["detail"][$i]["product_id"]." and d.balance_quantity >0 and p.cancel_date is null order by p.purchase_date desc";
      $products = mysqli_query($mysqli,$sql);	
      $x=0;
      foreach($products as $product_rec){   
        $quan=$quantity;
        if($product_rec["balance_quantity"]>=$quantity){
           $quan=$quantity;  
        }else{
          $quan=$product_rec["balance_quantity"];          
        }
        if($x>0) {$detail_sql=$detail_sql." , ";}
        ++$x;
        $detail_sql =$detail_sql. "  (".$sale_id.",".$this->post_data["detail"][$i]["product_id"].",".$quan.",".$this->post_data["detail"][$i]["sale_price"].",".$product_rec["id"].")";

        
        //Update purchase balance
        $sql="update purchase_detail set balance_quantity=balance_quantity -".$quan." where id=".$product_rec["id"];
        $result = mysqli_query($mysqli,$sql);
        
        if (!$result) {
          http_response_code(404);       
          if (!$result) { handleRollBack($mysqli);}     
          die('<br>'.$sql.'<br>'.mysqli_error($mysqli));
        }
        
        $quantity=$quantity-$quan;
        if($quantity==0){break;}
        
      }
  }
  
  $result = mysqli_query($mysqli,$detail_sql);
  if (!$result) {
    echo $detail_sql;
    http_response_code(404);       
    if (!$result) { handleRollBack($mysqli);}     
    die('<br>'.$sql.'<br>'.mysqli_error($mysqli));
  }
  
  //Update product balance
  $sql="update product as p left join (select product_id,sum(balance_quantity) as bal from purchase_detail as d left join purchase_order as p on d.purchase_order_id=p.id where p.cancel_date is null group by product_id) as d on p.id=d.product_id set p.balance_quantity=d.bal ";
  $result = mysqli_query($mysqli,$sql);
  if (!$result) {
    echo $sql;
    http_response_code(404);       
    if (!$result) { handleRollBack($mysqli);}     
    die('<br>'.$sql.'<br>'.mysqli_error($mysqli));
  }
  mysqli_commit($mysqli);
}  
if ( $this->requestUrl[2]=='reprint'){
  $reprint="REPRINT";
  $sale_id=$this->requestUrl[3];
}

$sql="SELECT o.*,u.user_name from (sale_order as o left join user_profile as u on o.created_by=u.id) where o.id=$sale_id";
$result = mysqli_query($mysqli,$sql);
if (!$result) {
  echo $sql;
  http_response_code(404);       
  if (!$result) { handleRollBack($mysqli);}     
  die('<br>'.$sql.'<br>'.mysqli_error($mysqli));
}
$sale_rec= mysqli_fetch_object($result);	

$sql="SELECT d.sale_order_id,d.product_id,sum(d.quantity) as quantity,avg(d.sale_price) as sale_price ,p.description FROM `sale_detail` as d left join product as p on d. product_id=p.id where d.sale_order_id=$sale_id group by d.sale_order_id,d.product_id,p.description ";
$details = mysqli_query($mysqli,$sql);	


  $date = new DateTime('NOW');  
  if (!$new_receipt ){
    $date = new DateTime($sale_rec->created_date.' '.sprintf('%06d',$sale_rec->created_time));
  }

    //echo($bl_id.'<br>');
    //echo(preg_replace('/[^a-z0-9_]+/i','',$$this->requestUrl[2]).'<br>');
    //exit();
    $rate_str='Rate';
    $discount_str='Discount';
    if(!$show_discount){
      $rate_str='';
      $discount_str='';
    }

    $pdf->AddPage();

    $pdf->Ln();
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell( 50, 5,'Product', 'B');
    $pdf->Cell( 40, 5,"Unit Price", 'B',0,'R');
    $pdf->Cell( 30, 5,"Quantity", 'B',0,'R');
    $pdf->Cell( 40, 5,'Amount ', 'B',1,'R');
    $pdf->SetFont('Arial','',12);
    $total=0;  
    
    foreach($details as $detail_rec){ 
      $pdf->Cell( 60, 8,$detail_rec["description"]);
      $pdf->SetFont('Arial','',10);
      //$pdf->Cell( 20, 8,$us_amt_str, 0,0,'C');
      $cost=$detail_rec["quantity"] * $detail_rec["sale_price"];
      $pdf->SetFont('Arial','',12);
      $pdf->Cell( 30, 8,number_format($detail_rec["sale_price"],2), 0,0,'R');
      $pdf->Cell( 30, 8,number_format($detail_rec["quantity"],0), 0,0,'R');
      $pdf->Cell( 40, 8,number_format($cost,2), 0,1,'R');
      $total +=$cost;
    }
    $pdf->Cell( 38, 10,'', 'TB',0);
    $pdf->Cell( 22, 10,'', 'T',0); //TBC
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell( 40, 10,'Total ', 'T',0,'R');
    $pdf->Cell( 60, 10,number_format($total,2), 'T',1,'R');
    $pdf->Cell( 60, 10,'Cashier : '.$sale_rec->user_name, 0,0);
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell( 30, 8,$payment_type_no, 0,0,'R');
    $pdf->Cell( 70, 8,$payment_type, 0,1,'R');
  

if ($this->requestUrl[3]=='blob'){
  $pdf->Output();
}else{   
  $filename="./report/printout/".$this->claims['user']."_print.pdf";
  $pdf->Output($filename,'F');
 echo "/report/printout/".$this->claims['user']."_print.pdf";
}

function handleRollBack($mysqli){
  mysqli_rollback($mysqli);
  die(mysqli_error($mysqli));
}



?>
