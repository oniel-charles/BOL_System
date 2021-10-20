
<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {
    case 'date_range':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT p.*,u.user_name from (sale_order as p left join user_profile as u on p.created_by=u.id)  where created_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    case 'excel':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT p.*,u.user_name,s.supplier_name from ((sale_order as p left join user_profile as u on p.created_by=u.id) left join supplier as s on p.supplier_id=s.id) where sale_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    
    case 'cancel':         
      $sql=" update `purchase_detail` as p  left join sale_detail as s on p.id=s.purchase_detail_id  set `balance_quantity` =`balance_quantity`+ s.quantity where s.sale_order_id=".$this->requestUrl[2];
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }
      date_default_timezone_set('America/Jamaica');
    
      $sql=" update sale_order set cancel_date=".date('Ymd').",cancel_time=".date('hi').",cancel_by=".$this->claims['id']." where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
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
