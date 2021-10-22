
<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {
    case 'date_range':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT * from receipt where receipt_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    case 'excel':    
    //$tablesjson->{"address"}->{"pkey"}
    $sql="SELECT v.arrival_date,s.vessel_name,r.cancel_date,r.cancel_time,x.user_name as cancel_by,u.user_name as cashier,r.cancelled,r.id,r.payee,r.receipt_date,r.receipt_total,p.port_name,b.bill_of_lading_number FROM ((((((receipt as r left join bill_of_lading as b on r.billoflading_id=b.id) left join port as p on b.port_of_loading=p.id) left join user_profile as u on r.created_by=u.id) left join user_profile as x on r.cancel_by=x.id) left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) where r.receipt_date between ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2])." and ".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
    break;
    
    case 'cancel':         

      date_default_timezone_set('America/Jamaica');
      $sql=" update  bill_of_lading set receipt_processed=0 where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      } 
      $sql=" update receipt set cancelled=1,cancel_date=".date('Ymd').",cancel_time=".date('hi').",cancel_by=".$this->claims['id']." where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      }
      exit();
      break;
    case 'DELETE':
      $sql = "delete from `$table`xx where $where"; 
      break;
  }

?>
