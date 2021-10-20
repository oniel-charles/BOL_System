
<?php
$mysqli=$this->db->conn;
switch ($this->requestUrl[1]) {  
  case 'DELETE':
      $sql = " delete some thing"; 
      break;
  case 'cancel':
          date_default_timezone_set('America/Jamaica');
          mysqli_autocommit($mysqli,FALSE);
          $sql="SELECT b.id,b.voyage_id ,p.id as master_id,port.port_name,v.voyage_number,v.arrival_date,b.bill_of_lading_number as refnum,p.bill_of_lading_number,b.consignee_name,b.consignee_address,b.shipper_name,b.shipper_address,s.vessel_name  FROM ((((`bill_of_lading` as b left join voyage as v on b.voyage_id=v.id) left join vessel as s on v.vessel_id=s.id) left join port  on b.port_of_delivery=port.id) left join (select id,bill_of_lading_number from bill_of_lading where parent_bol=1) as p on b.master_bol_id=p.id) WHERE b.id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
          $result = mysqli_query($mysqli,$sql);
          $rec= mysqli_fetch_object($result);
          if (!$result) {
            http_response_code(404);
            die($sql.'<br>'.mysqli_error($mysqli));
           }	
          $sql=" update  bill_of_lading set order_processed=0 where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[3]);
          $result = mysqli_query($mysqli,$sql);
          if (!$result) { handleRollBack($mysqli);}          
          $sql=" update shipment_order set cancelled=1,cancel_date=".date('Ymd').",cancel_time=".date('hi').",cancel_by=".$this->claims['id']." where id=".preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[2]);          
          $result = mysqli_query($mysqli,$sql);      
          echo mysqli_error($mysqli);
          if (!$result) { handleRollBack($mysqli);}
          mysqli_commit($mysqli);
          $sql=" update  bill_of_lading set order_processed=0 where id=".$rec->master_id." and id not in (select master_bol_id from (select master_bol_id from bill_of_lading  where master_bol_id=".$rec->master_id." and order_processed=1) as t)";
          $result = mysqli_query($mysqli,$sql);
          //echo $sql;
          if (!$result) {
            handleRollBack($mysqli);
          }
          break;
  }

  function handleRollBack($mysqli){
    mysqli_rollback($mysqli);
    die(mysqli_error($mysqli));
  }

?>
