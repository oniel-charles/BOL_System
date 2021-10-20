
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    $sql="select * ,bill_of_lading_number as description from tally_detail ";
    break;
    case 'merge':
      //Get Company
      $mysqli=$this->db->conn; 
      mergerDetail($mysqli,$this->requestUrl[2]);
      http_response_code(200);
      die();
    break;
    case 'create_list':
          //Get Company
      $mysqli=$this->db->conn; 
      $tally_id=0;    
      $sql="SELECT b.voyage_number,t.* FROM booking as b left join tally as t on b.id=t.booking_id where b.id=".$this->requestUrl[2];
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
      } 
      $tally_rec= mysqli_fetch_object($result);	 
      if($tally_rec->booking_id==null){    
        $sql="INSERT INTO `tally` ( `start_date`, `end_date`, `status`, `booking_id`) ";
        $sql=$sql." VALUES (0,0,'Not Started',".$this->requestUrl[2].")";
        $result = mysqli_query($mysqli,$sql);
        if (!$result) {
          http_response_code(404);
          die($sql.'<br>'.mysqli_error($mysqli));
        }
        $tall_id=mysqli_insert_id($mysqli);
      }else{
        $tall_id=$tally_rec->id;
      }
      //Merge tally entry with detail
      mergerDetail($mysqli,$this->requestUrl[2]);
      $sql="insert into tally_detail (`tally_id`,`billoflading_detail_id`,`bill_of_lading_number`,`consignee_name`,`package_type_id`) select $tall_id as tally_id,d.id,b.bill_of_lading_number,b.consignee_fname,d.package_type_id from ((bill_of_lading_detail as d left join bill_of_lading as b on d.billoflading_id=b.id) left join tally_detail as t on d.id=t.billoflading_detail_id) where b.booking_id=".$this->requestUrl[2]." and t.id is null and b.parent_bol=0 ";    
      $result = mysqli_query($mysqli,$sql);
      if (!$result) {
          http_response_code(404);
          die($sql.'<br>'.mysqli_error($mysqli));
       }
       $sql="select d.*,h.bill_of_lading_number as bl_num from (((tally_detail as d left join tally as t on d.tally_id=t.id) left join bill_of_lading_detail as b on d.`billoflading_detail_id`=b.id) left join bill_of_lading as h on b.billoflading_id=h.id and t.booking_id=h.booking_id) where t.booking_id=".$this->requestUrl[2]." order by d.bill_of_lading_number";
    break;
    case 'list':    
      $sql="select d.*  from tally_detail as d left join tally as t on d.tally_id=t.id where t.booking_id=".$this->requestUrl[2]." order by d.bill_of_lading_number";
      break;
    case 'excel':    
    $sql="SELECT v.vessel_me,v.vessel_code,v.lloyd_number,c.country_name,v.id from vessel as v left join country as c on v.country_id=c.id  order by vessel_name ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }
  function mergerDetail($mysqli,$booking_id){
    $sql="update tally_detail as t left join (select d.id,b.bill_of_lading_number,d.package_type_id from bill_of_lading as b left join bill_of_lading_detail as d on b.id=d.billoflading_id where b.booking_id=$booking_id) as l on t.bill_of_lading_number=l.bill_of_lading_number  and t.package_type_id=l.package_type_id set t.billoflading_detail_id=l.id where l.id is not null";
     $result = mysqli_query($mysqli,$sql);
      if (!$result) {
        http_response_code(404);
        die($sql.'<br>'.mysqli_error($mysqli));
     }
  }

?>
