
<?php
switch ($this->requestUrl[1]) {
    case 'select':    
    $sql="select * ,status as description from tally ";
    break;
    case 'booking':    
      $sql="select *  from tally where booking_id=".$this->requestUrl[2];
    break;
    case 'status':    
      $sql="select t.id,t.start_date,t.end_date,t.status,sum(d.number_of_items) as number_of_items  from tally_detail as d left join tally as t on d.tally_id=t.id  where booking_id=".$this->requestUrl[2]." group by t.start_date,t.end_date,t.status";
    break;
    case 'tally_result':    
      $sql="SELECT 0 as id,t.bill_of_lading_number,b.package_type_id as b_package,b.number_of_items as b_num ,t.number_of_items as t_num, t.package_type_id as t_package FROM ((tally_detail as t  left join `tally` as h on t.tally_id=h.id ) left join bill_of_lading_detail as b on t.billoflading_detail_id =b.id) where h.booking_id=".$this->requestUrl[2]; 
      $sql .=" union  SELECT  0 as id,h.bill_of_lading_number,b.package_type_id as b_package,b.number_of_items as b_num ,t.number_of_items as t_num, t.package_type_id as t_package FROM ((bill_of_lading_detail as b left join bill_of_lading as h on b.billoflading_id=h.id) left join tally_detail as t on b.id=t.billoflading_detail_id)  where h.booking_id=".$this->requestUrl[2]." and t.id is null and h.parent_bol=0";
    break;
    case 'excel':    
    $sql="SELECT v.vessel_me,v.vessel_code,v.lloyd_number,c.country_name,v.id from vessel as v left join country as c on v.country_id=c.id  order by vessel_name ";
    break;
    case 'DELETE':
      $sql = "delete from ddd`$table` where $where"; 
      break;
  }
  
?>
