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
    global $containers;
    global $item_cnt;
    global $container_size;
    global $voyage_rec;

    $this->SetFont('Arial','B',12);
    $this->Ln();
    

    $this->Cell(110,5,'Container No : '.$containers[$current_rec["master_bl"]],0,0,'L',false);
    $this->Cell(70,5,'Bill of Lading No : '.$current_rec["master_bl"],0,1,'L',false);
    $this->SetFont('Arial','',12);
    $this->Ln();    
    $this->Cell(110,5,' 1 X '. $container_size[$containers[$current_rec["master_bl"]]].' Cont. Stc. '.$item_cnt->total_items.' pcs',0,0);
    $this->SetLeftMargin(7);    
    $this->Cell(70,5,'MANIFEST # '.$voyage_rec->manifest_number,0,1);

$this->Line(7,70,7,275); //vertical
$this->Line(7,70,205,70);  //horizontal
$this->Line(7,83,205,83);  //horizontal
$this->Line(7,275,205,275);  //horizontal
$this->Line(205,70,205,275); //vertical

$this->SetY(72);

$this->SetFont('Arial','B',11);
$this->Cell(23,5,'ITEM',0,0);
$this->Cell(47,5,'CONSIGNEE',0,0);
$this->Cell(40,5,'SHIPPER',0,0);
$this->Cell(15,5,'NO. OF',0,0);
$this->Cell(38,5,'DESCRIPTION',0,0);
$this->Cell(17,5,'CUB.',0,0);
$this->Cell(17,5,'WEIGHT',0,1);

$this->Cell(23,5,'NO.',0,0);
$this->Cell(47,5,'',0,0);
$this->Cell(40,5,'',0,0);
$this->Cell(15,5,'ITEMS',0,0);
$this->Cell(38,5,'',0,0);
$this->Cell(17,5,'CM.',0,0);
$this->Cell(17,5,'KG',0,0);
$this->Cell(1,5,'',0,1);

$this->Line(23,70,23,275); //vertical
$this->Line(70,70,70,275); //vertical
$this->Line(117,70,117,275); //vertical
$this->Line(132,70,132,275); //vertical/
$this->Line(170,70,170,275); //vertical/
$this->Line(187,70,187,275); //vertical/



$this->SetY(85);
 


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
  global $voyage_rec;
  global $company_rec;
  global $bill_rec;
  global $run_date;
  global $receipt_number;
  global $table_data;
  global $table_index;
  global $group_total;
  global $request;
  global $report_filter;
  global $current_rec;  
  global $containers;

$this->SetFont('Arial','B',18); 
$this->Image('../img/company_icon.jpg',7,10,-200);
$this->Cell(20,10,'',0,0);
$this->Cell(150,10,$company_rec->company_name,0,1);
$this->Cell(20,10,'',0,0);
$this->SetFont('Arial','',12);
$this->MultiCell( 100, 5, $company_rec->company_address, 0);
$this->Line(7,7,7,35); //vertical
$this->Line(7,7,195,7);  //horizontal

$this->Line(195,7,195,35); //vertical
$this->Line(7,35,195,35);  //horizontal

$this->SetY(35);
$this->SetFont('Arial','B',12);
$this->Cell( 110, 5,'', 0,0);
$this->Cell(70,10,'TEL : '.$company_rec->phone,0,1);

$this->Cell(110, 5,'Vessel / Voyage : '.$voyage_rec->vessel_name.' / '.$voyage_rec->voyage_number, 0,0,'L');
$this->Cell(70, 5,'Reported Date: '.$voyage_rec->arrival_date->format('d/m/Y'), 0,1);

//$this->Cell(20, 5,'Voyage: ', 0,0,'R');
//$this->Cell(40, 5,$voyage_rec->voyage_number, 0,1,'L');
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

global $voyage_rec;
global $company_rec;
global $bill_rec;
global $run_date;
global $receipt_number;
global $table_data;
global $table_index;
global $group_total;
global $request;
global $report_filter;

global $current_rec;
global $containers;
global $item_cnt;
global  $container_size;


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

  $cur_date= date('Ymd') ;

  $sql="SELECT * from charge_item_rate where charge_item_id=-1 and effective_date <=".$cur_date."  order by effective_date desc limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $gct_rec= mysqli_fetch_object($result);	

  // EXchange Rate 
  $sql="SELECT * from currency_rate where currency_id=2 and effective_date <=".$cur_date." order by effective_date desc limit 1 " ;
  $result = mysqli_query($mysqli,$sql);
  $us_rate= mysqli_fetch_object($result);	

  //Get Company 
  $sql="SELECT  * from company limit 1";
 $company = mysqli_query($mysqli,$sql);
 $company_rec= mysqli_fetch_object($company);	
 $voyage_id= $this->requestUrl[2];

 //Get Voyage 
   $sql="SELECT a.id,a.manifest_number,a.voyage_number,a.arrival_date,v.vessel_name FROM `voyage` as a left join vessel as v on a.vessel_id=v.id WHERE a.id=$voyage_id";
   $voyage = mysqli_query($mysqli,$sql);
   $voyage_rec= mysqli_fetch_object($voyage);	
   $voyage_rec->arrival_date= new DateTime($voyage_rec->arrival_date);

   //Get Items   
   $sql="SELECT sum(number_of_items) as total_items FROM `bill_of_lading_detail` WHERE billoflading_id in (select id from bill_of_lading where voyage_id=$voyage_id)";
   $result = mysqli_query($mysqli,$sql);
   $item_cnt= mysqli_fetch_object($result);	
   //Get charges 
   $sql="SELECT b.attract_gct,b.currency_id,b.billoflading_id,amount,item_code,description FROM `bill_of_lading_other_charge` as b left join charge_item as c on b.charge_item_id=c.id WHERE b.billoflading_id in (select id from bill_of_lading where voyage_id=$voyage_id)";
   $charges = mysqli_query($mysqli,$sql);
   $charges_data = array();
   foreach($charges as $charge_rec){ 
        if(!isset($charges_data[$charge_rec["billoflading_id"]])) {
          $charges_data[$charge_rec["billoflading_id"]] = array();
        }
        $charges_data[$charge_rec["billoflading_id"]][] = $charge_rec;

    }
      //Get details
   $sql="SELECT * FROM `bill_of_lading_detail` as b WHERE b.billoflading_id in (select id from bill_of_lading where voyage_id=$voyage_id)";
   $result = mysqli_query($mysqli,$sql);
   $details_data = array();
   foreach($result as $detail_rec){ 
        if(!isset($details_data[$detail_rec["billoflading_id"]])) {
          $details_data[$detail_rec["billoflading_id"]] = array();
        }
        $details_data[$detail_rec["billoflading_id"]][] = $detail_rec;

    }
   
    //containers 
    $sql="select size_type_code,container_number,bill_of_lading_number FROM ((bill_of_lading as b left join `bill_of_lading_container` as c on b.id=c.billoflading_id) left join container_size_type as s on c.container_size_type_id=s.id) where voyage_id=$voyage_id and parent_bol=1 ";
    $tmp = mysqli_query($mysqli,$sql);
    $containers = array();
    $container_size = array();
    foreach($tmp as $rec){
      $containers[$rec["bill_of_lading_number"]]=$rec["container_number"];        
      $container_size[$rec["container_number"]]=$rec["size_type_code"];      
    }

 $sql="SELECT data_value from system_values where code='freight_id'";
 $result = mysqli_query($mysqli,$sql);
 $freight= mysqli_fetch_object($result);	

   $order_number=0;
   $user=$claims['full_name'];



 $sql="SELECT b.id,m.master_bl, b.`bill_of_lading_number`,`consignee_name`,`consignee_address`,`consignee_phone_num`,`shipper_name`,`shipper_address`,`shipper_phone_num`,`notify_name`,`notify_address`,`notify_phone_num` FROM `bill_of_lading` as b left join (select id,bill_of_lading_number as master_bl from bill_of_lading) as m on b.master_bol_id=m.id WHERE voyage_id=$voyage_id and b.parent_bol=0 order by m.master_bl,b.id " ;
 $table_data = mysqli_query($mysqli,$sql);	
 foreach($table_data as $current_rec){ break;}
   
// die if SQL statement failed
if (!$table_data) {
  http_response_code(404);
  die($sql.'<br>'.mysqli_error($mysqli));
}

$run_date = new DateTime('NOW');  
$pdf = new PDF();
$pdf->AddPage();

$table_index=0;
$master_bl='';
$pdf->SetFont('Arial','',10);   
 
foreach($table_data as $current_rec){
  
  $table_index++;  
  /*
    $this->Cell(50,5,'Consignee',0,0,'C',true);
    $this->Cell(50,5,'Shipper',0,0,'L',true);
    $this->Cell(50,5,'Notify',0,0,'L',true);
    $this->Cell(50,5,'Charges',0,0,'L',true);
    $this->Cell(30,5,'Amount',0,1,'R',true);
*/
if($table_index>1){
   $pdf->Cell(1,1,'',0,1);
   $pdf->Ln();
}

 $y=$pdf->GetY();  
  if ($y>245 || ($master_bl!=$current_rec["master_bl"] && $table_index>1)){
    $pdf->AddPage();
  }
  $master_bl=$current_rec["master_bl"];

  $pdf->Cell(16,5,$current_rec["bill_of_lading_number"],0,0,'L');
  $pdf->Cell(46,5,substr($current_rec["consignee_name"],0,22),0,0,'L');
  $pdf->Cell(48,5,substr($current_rec["shipper_name"],0,22),0,0,'L'); 
  $no_items= $details_data[$current_rec["id"]][0]["number_of_items"];
  $pdf->Cell(15,5,$no_items,0,0,'C');
  $x=$pdf->GetX();
  $addressPos =$pdf->GetVerticalPosition();
  $item_ypos =$pdf->GetVerticalPosition();
  $addressPos["y"]=$addressPos["y"]+5;
  $pdf->MultiCell(38, 5,$details_data[$current_rec["id"]][0]["Description_of_goods"], 0,'L');  
  
  $furthestPos = $pdf->GetVerticalPosition();
  $pdf->SetVerticalPosition( $item_ypos); 
    $pdf->SetFont('Arial','',10);  
  $pdf->SetX($x+38);
  $pdf->Cell(17,5,number_format($details_data[$current_rec["id"]][0]["measure"]/35.315,3),0,0,'R');
  $pdf->Cell(17,5,$details_data[$current_rec["id"]][0]["weight"],0,1,'R');
  $total=0;
  
 
  for ($i = 1; $i < count($details_data[$current_rec["id"]]); $i++) {
       
    $pdf->SetVerticalPosition( $furthestPos ); 
    $pdf->SetX($x-15); 
    $pdf->Cell(15,5,$details_data[$current_rec["id"]][$i]["number_of_items"],0,0,'C');
    $x=$pdf->GetX();
    $item_ypos =$pdf->GetVerticalPosition();
    $pdf->MultiCell(38, 5,$details_data[$current_rec["id"]][$i]["Description_of_goods"], 0,'L');  
    
    $furthestPos = $pdf->GetVerticalPosition();    
    $pdf->SetVerticalPosition( $item_ypos); 
    $pdf->SetFont('Arial','',10);  
    $pdf->SetX($x+38);
    $pdf->Cell(17,5,$details_data[$current_rec["id"]][$i]["measure"],0,0,'R');
    $pdf->Cell(17,5,$details_data[$current_rec["id"]][$i]["weight"],0,1,'R');
   }
  
  $pdf->SetVerticalPosition( $addressPos ); 
  $pdf->SetFont('Arial','',10);  
  $pdf->Cell(16,5,'',0,0);
  $x=$pdf->GetX();
  
  $pdf->MultiCell(47, 5, trim($current_rec["consignee_address"]), 0,'L');  
  $furthestPos = $pdf->FurthestVerticalPosition( $pdf->GetVerticalPosition(), $furthestPos );
  $margin_x=$pdf->GetX();
  $x+=47;
  $pdf->SetVerticalPosition( $addressPos ); 
  $pdf->SetX($x);
  $pdf->MultiCell(47, 5, trim($current_rec["shipper_address"]), 0,'L');
  $furthestPos = $pdf->FurthestVerticalPosition( $pdf->GetVerticalPosition(), $furthestPos );
  $pdf->SetVerticalPosition( $furthestPos );
  $pdf->SetX($margin_x);
}





$pdf->Ln();


$pdf->Output();

?>
