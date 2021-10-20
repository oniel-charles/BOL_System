
<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {
    case 'date_range':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT p.*,u.user_name,s.supplier_name from ((purchase_order as p left join user_profile as u on p.created_by=u.id) left join supplier as s on p.supplier_id=s.id) where purchase_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    case 'excel':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT p.*,u.user_name,s.supplier_name from ((purchase_order as p left join user_profile as u on p.created_by=u.id) left join supplier as s on p.supplier_id=s.id) where purchase_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    
    case 'cancel':         
      
      $sql="SELECT  *  FROM `purchase_detail` WHERE `quantity`-`balance_quantity` <> 0 and `purchase_order_id`=".$this->requestUrl[2];      
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }
      if(mysqli_num_rows($result)!==0){
        http_response_code(404);
        die("Cannot cancel: Sales were made from this batch");
      }

      date_default_timezone_set('America/Jamaica');
    
      $sql=" update purchase_order set cancel_date=".date('Ymd').",cancel_time=".date('hi').",cancel_by=".$this->claims['id']." where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
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
      
      exit();
      break;
    case 'DELETE':
      $sql = "delete from `$table`xx where $where"; 
      break;
  }

?>
